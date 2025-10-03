function setLanguage(lang) {
  fetch(`lang/${lang}.json`)
    .then(response => response.json())
    .then(data => {
      // 各 ID に対応するテキストを挿入
      document.getElementById("home").innerText = data.home;
      document.getElementById("find_rooms").innerText = data.find_rooms;
      document.getElementById("list_property").innerText = data.list_property;
      document.getElementById("about_us").innerText = data.about_us;
      document.getElementById("contact").innerText = data.contact;
      document.getElementById("sign_in").innerText = data.sign_in;
      document.getElementById("sign_up").innerText = data.sign_up;
    });
}
