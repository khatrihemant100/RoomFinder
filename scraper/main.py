from fastapi import FastAPI
from fastapi.responses import JSONResponse
import json
import os

app = FastAPI(title="Rental API", version="1.0")

DATA_PATH = "data/suumo_data.json"

@app.get("/api/rentals")
def get_rentals():
    """SUUMOスクレイピング結果を返す"""
    if not os.path.exists(DATA_PATH):
        return JSONResponse({"error": "data file not found"}, status_code=404)

    with open(DATA_PATH, "r", encoding="utf-8") as f:
        data = json.load(f)

    return JSONResponse(content=data)

@app.get("/")
def root():
    return {"message": "Rental API is running", "endpoint": "/api/rentals"}