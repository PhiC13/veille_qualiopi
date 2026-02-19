# import_rss_to_db.py

from datetime import datetime
from dateutil import parser as dateparser  # robuste pour RSS
from rss_qualiopi.aggregate import generate_local_sources, aggregate_grouped
from rss_qualiopi.db import (
    insert_article,
    insert_activity,
    insert_pipeline_log,
    get_source_id
)


def import_rss_into_database():
    try:
        # 1. Lire les flux externes et générer les flux locaux
        local_files = generate_local_sources()

        # 2. Agréger tous les flux
        data = aggregate_grouped(local_files)
        articles = data["global"]

        added = 0

        # 3. Insérer dans SQL
        for a in articles:
            source_url = a["source"]
            source_id = get_source_id(source_url)

            if not source_id:
                insert_pipeline_log("ERROR", f"Source inconnue : {source_url}")
                continue

            # Parsing robuste de la date RSS
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

            added += insert_article(article)

        # 4. Historique d'activité : nombre d’articles ajoutés
        insert_activity(added)

        # 5. Log pipeline
        insert_pipeline_log("OK", f"{added} articles ajoutés depuis les flux RSS")

        print(f"Import terminé : {added} nouveaux articles")

    except Exception as e:
        insert_pipeline_log("ERROR", str(e))
        raise


if __name__ == "__main__":
    import_rss_into_database()
