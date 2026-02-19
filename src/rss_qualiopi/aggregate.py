import requests
import feedparser
from datetime import datetime
from dateutil import parser as dateparser
import time

from rss_qualiopi.db import (
    get_all_sources,
    insert_article,
    insert_activity,
    insert_pipeline_log,
    get_pipeline_logs  # À AJOUTER dans db.py
)


# ------------------------------------------------------------
#  Lecture d’un flux RSS
# ------------------------------------------------------------
def read_rss(url: str, run_id: int):
    try:
        resp = requests.get(
            url,
            timeout=10,
            headers={"User-Agent": "Mozilla/5.0"}
        )
        resp.raise_for_status()
        feed = feedparser.parse(resp.content)
    except Exception as e:
        insert_pipeline_log("ERROR", f"Erreur RSS sur {url}: {e}", run_id)
        return []

    articles = []
    for e in feed.entries:
        articles.append({
            "title": e.get("title", ""),
            "link": e.get("link", ""),
            "date": e.get("published", "") or e.get("updated", ""),
            "description": e.get("summary", ""),
            "source_url": url,
        })

    return articles


# ------------------------------------------------------------
#  Pipeline principal
# ------------------------------------------------------------
def run_full_import():
    run_id = int(time.time())

    try:
        # 1. Lire toutes les sources
        sources = get_all_sources()
        insert_pipeline_log("INFO", f"{len(sources)} sources trouvées dans SQL", run_id)

        total_added = 0

        # ------------------------------------------------------------
        # 2. Lire les flux externes
        # ------------------------------------------------------------
        for src in sources:
            url = src["url"]
            source_id = src["id"]
            name = src.get("name", f"Source {source_id}")

            # On ignore COSMOS ici
            if "rss_cosmos.xml" in url:
                continue

            insert_pipeline_log("INFO", f"Lecture du flux : {url} ({name})", run_id)

            # Lire le flux
            articles = read_rss(url, run_id)
            count_read = len(articles)
            count_added = 0

            # Vérifier si une erreur a été loguée
            logs = get_pipeline_logs(run_id)
            flux_error = any(
                log["status"] == "ERROR" and url in log["message"]
                for log in logs
            )

            if flux_error:
                insert_pipeline_log("ERROR", f"{name} : échec du flux", run_id)
                continue

            # Import normal
            for a in articles:
                try:
                    published = dateparser.parse(a["date"])
                except Exception:
                    published = datetime.now()

                article = {
                    "title": a["title"],
                    "url": a["link"],
                    "published_at": published,
                    "source_id": source_id
                }

                count_added += insert_article(article)

            total_added += count_added

            insert_pipeline_log(
                "OK",
                f"{name} : {count_read} lus, {count_added} nouveaux",
                run_id
            )

        # ------------------------------------------------------------
        # 3. Flux local COSMOS
        # ------------------------------------------------------------
        cosmos = next(
            (s for s in sources if "rss_cosmos.xml" in s["url"]),
            None
        )

        if cosmos:
            insert_pipeline_log("INFO", "Lecture du flux : COSMOS (local)", run_id)

            local_articles = read_rss(cosmos["url"], run_id)
            cosmos_read = len(local_articles)
            cosmos_added = 0

            for a in local_articles:
                try:
                    published = dateparser.parse(a["date"])
                except Exception:
                    published = datetime.now()

                article = {
                    "title": a["title"],
                    "url": a["link"],
                    "published_at": published,
                    "source_id": cosmos["id"]
                }

                cosmos_added += insert_article(article)

            total_added += cosmos_added

            insert_pipeline_log(
                "OK",
                f"COSMOS : {cosmos_read} lus, {cosmos_added} nouveaux",
                run_id
            )

        else:
            insert_pipeline_log("WARN", "Source COSMOS introuvable dans SQL", run_id)

        # ------------------------------------------------------------
        # 4. Historique d’activité
        # ------------------------------------------------------------
        insert_activity(total_added)

        # ------------------------------------------------------------
        # 5. Log final
        # ------------------------------------------------------------
        insert_pipeline_log("OK", f"{total_added} articles importés", run_id)
        print(f"Pipeline terminé : {total_added} articles importés, {flux_ok} flux OK, {flux_error} flux en erreur")

    except Exception as e:
        insert_pipeline_log("ERROR", str(e), run_id)
        raise


if __name__ == "__main__":
    run_full_import()
