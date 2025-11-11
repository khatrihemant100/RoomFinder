import urllib
import requests
from bs4 import BeautifulSoup
import json
import time
import os
import re

# URL生成ヘルパー
def build_suumo_url(base_url: str, params: dict):
    query = urllib.parse.urlencode(params)
    return f"{base_url}?{query}"

# URL例
# https://suumo.jp/jj/chintai/ichiran/FR301FC001/?ar=030&bs=040&ta=14&sc=14112&sngz=&po1=25&pc=10

# ===============================
# 設定
# ===============================
PARAMS = {
    "ar": "030",     # エリア（首都圏）
    "bs": "040",     # 建物種別: 40=賃貸マンション
    "ta": "14",      # 都道府県: 14=神奈川
    "sc": "14112",   # 市区: 14112=横浜市神奈川区
    "cb": "0.0",     # 下限賃料（万円）
    "ct": "8.0",     # 上限賃料（万円）
    "et": "15",      # 徒歩時間（分）
    "mb": "20",      # 専有面積下限（m2）
    "mt": "9999999", # 専有面積上限
    "po1": "25",     # 並び順: 新着順
    "pc": "10",      # 表示件数
    "sngz": "",      # 一人暮らし向けなど
}

results = []

BASE_URL = "https://suumo.jp/jj/chintai/ichiran/FR301FC001/"
# URL生成
url = build_suumo_url(BASE_URL, PARAMS)
print("🔍 Target URL:", url)

HEADERS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"}
OUTPUT_PATH = "data/suumo_" + PARAMS["sc"] + ".json"
os.makedirs("data", exist_ok=True)

def clean(text):
    return re.sub(r"\s+", " ", text.strip()) if text else ""

def yen_to_number(text):
    """'27万円' → 270000, '8.5万円' → 85000"""
    text = text.replace(",", "").strip()
    m = re.match(r"([\d.]+)万円", text)
    if m:
        return int(float(m.group(1)) * 10000)
    m = re.match(r"([\d,]+)円", text)
    if m:
        return int(m.group(1).replace(",", ""))
    return None

def area_to_number(text):
    """'27m2' → 27, '8.5m2' → 8.5"""
    text = text.replace(",", "").strip()
    m = re.match(r"([\d.]+)m2", text)
    if m:
        return float(m.group(1))
    return None

def parse_fee(fee_text):
    """例: '27万円20000円' → rent, management_fee"""
    fee_text = fee_text.replace("\n", "")
    rent_match = re.search(r"([\d.]+万円)", fee_text)
    mgmt_match = re.search(r"([\d,]+円)", fee_text)
    rent = yen_to_number(rent_match.group(1)) if rent_match else None
    mgmt = yen_to_number(mgmt_match.group(1)) if mgmt_match else None
    return rent, mgmt

def parse_deposit_key(text):
    """例: '敷2ヶ月 礼1ヶ月' or '敷／礼' → deposit, key_money"""
    text = text.replace(" ", "")
    deposit = ""
    key_money = ""
    dep_match = re.search(r"敷\s*([0-9.]+)(ヶ月|万|円)?", text)
    key_match = re.search(r"礼\s*([0-9.]+)(ヶ月|万|円)?", text)
    if dep_match:
        deposit = f"{dep_match.group(1)}{dep_match.group(2) or 'ヶ月'}"
    if key_match:
        key_money = f"{key_match.group(1)}{key_match.group(2) or 'ヶ月'}"
    return deposit, key_money

def parse_age(text):
    """例: '新築 14階建'→0, '築3年 7階建'→3"""
    m = re.search(r"築\s*([0-9]+)", text)
    if m:
        age = int(m.group(1))
    elif "新築" in text:
        age = 0
    else:
        age = None
    return age


# ===============================
# ページ取得
# ===============================
for page in range(1, 2):  # ページ数調整
    print(f"📄 Fetching page {page} ...")
    url = f"{url}&page={page}"
    res = requests.get(url, headers=HEADERS)
    if res.status_code != 200:
        print("❌ Failed:", res.status_code)
        continue

    soup = BeautifulSoup(res.text, "lxml")

    for prop in soup.select(".cassetteitem"):
        title = clean(prop.select_one(".cassetteitem_content-title").text)
        address = clean(prop.select_one(".cassetteitem_detail-col1").text)
        access = clean(prop.select_one(".cassetteitem_detail-col2").text.replace("\n", " / "))
        access = access.strip(" /")
        age_raw = clean(prop.select_one(".cassetteitem_detail-col3").text)
        age = parse_age(age_raw)

        # 各部屋情報
        for tr in prop.select("tbody tr"):
            tds = tr.select("td")
            if len(tds) < 8:
                continue

            rent = yen_to_number(clean(tr.select_one(".cassetteitem_price.cassetteitem_price--rent").text))
            management_fee = yen_to_number(clean(tr.select_one(".cassetteitem_price.cassetteitem_price--administration").text))
            deposit = yen_to_number(clean(tr.select_one(".cassetteitem_price.cassetteitem_price--deposit").text))
            key_money = yen_to_number(clean(tr.select_one(".cassetteitem_price.cassetteitem_price--gratuity").text))
            layout = clean(tr.select_one(".cassetteitem_madori").text)
            area = area_to_number(clean(tr.select_one(".cassetteitem_menseki").text))

            results.append({
                "title": title,
                "address": address,
                "access": access,
                "age": age,
                "rent": rent,
                "management_fee": management_fee,
                "deposit": deposit,
                "key_money": key_money,
                "layout": layout,
                "area": area
            })

    time.sleep(1)

# ===============================
# 保存
# ===============================
with open(OUTPUT_PATH, "w", encoding="utf-8") as f:
    json.dump(results, f, ensure_ascii=False, indent=2)

print(f"✅ {len(results)} 件の物件情報を {OUTPUT_PATH} に保存しました。")
