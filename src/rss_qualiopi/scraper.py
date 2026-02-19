import requests
from bs4 import BeautifulSoup
from datetime import datetime

URL = "https://www.cosmos-sports.fr/actualites"

HEADERS = {
    "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64)"
}

def fetch_articles():
    try:
        response = requests.get(URL, headers=HEADERS, timeout=10)
        response.raise_for_status()
    except Exception as e:
        print(f"[ERROR] Impossible de récupérer la page : {e}")
        return []

    soup = BeautifulSoup(response.text, "lxml")
    articles = []

    for item in soup.select("article.teaser"):
        title_tag = item.select_one("h2.teaser_title a")
        date_tag = item.select_one("time.teaser_date")
        desc_tag = item.select_one("p.teaser_desc")

        if not title_tag:
            continue

        title = title_tag.get_text(strip=True)
        link = title_tag["href"]
        if link.startswith("/"):
            link = "https://www.cosmos-sports.fr" + link

        date = date_tag.get("datetime") if date_tag else None
        description = desc_tag.get_text(strip=True) if desc_tag else ""

        articles.append({
            "title": title,
            "link": link,
            "date": date,
            "description": description
        })

    return articles
