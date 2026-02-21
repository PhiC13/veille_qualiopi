# db.py
import json
import os
from datetime import datetime
import mysql.connector
from mysql.connector import pooling


# ------------------------------------------------------------
#  Trouver automatiquement la racine du projet
# ------------------------------------------------------------
def find_project_root(start_path):
    """
    Remonte l'arborescence depuis start_path jusqu'à trouver config.json.
    Fonction ultra-robuste : fonctionne sur Windows, Linux, o2switch,
    GitHub Actions, et même si le projet est déplacé.
    """
    current = os.path.abspath(start_path)

    while True:
        candidate = os.path.join(current, "config.json")
        if os.path.isfile(candidate):
            return current

        parent = os.path.dirname(current)
        if parent == current:
            raise FileNotFoundError("config.json introuvable dans l'arborescence")

        current = parent


# ------------------------------------------------------------
#  Chargement config.json (singleton)
# ------------------------------------------------------------
def load_config():
    """
    Charge config.json une seule fois et le met en cache.
    """
    if not hasattr(load_config, "cache"):
        # db.py → rss_qualiopi → src → racine du projet
        db_dir = os.path.dirname(os.path.abspath(__file__))
        project_root = find_project_root(db_dir)
        config_path = os.path.join(project_root, "config.json")

        with open(config_path, "r", encoding="utf-8") as f:
            load_config.cache = json.load(f)

    return load_config.cache


# ------------------------------------------------------------
#  Pool de connexions MySQL (singleton)
# ------------------------------------------------------------
def get_pool():
    """
    Initialise un pool de connexions MySQL.
    """
    if not hasattr(get_pool, "pool"):
        cfg = load_config()

        get_pool.pool = pooling.MySQLConnectionPool(
            pool_name="rss_pool",
            pool_size=5,
            pool_reset_session=True,
            host=cfg["db_host"],
            user=cfg["db_user"],
            password=cfg["db_pass"],
            database=cfg["db_name"],
            charset="utf8mb4"
        )

    return get_pool.pool


def get_connection():
    """
    Retourne une connexion issue du pool.
    """
    return get_pool().get_connection()


# ------------------------------------------------------------
#  Articles
# ------------------------------------------------------------
def insert_article(article):
    conn = get_connection()
    cursor = conn.cursor()

    sql = """
        INSERT IGNORE INTO articles (title, url, published_at, source_id)
        VALUES (%s, %s, %s, %s)
    """

    cursor.execute(sql, (
        article["title"],
        article["url"],
        article["published_at"],
        article["source_id"]
    ))

    conn.commit()
    inserted = cursor.rowcount

    cursor.close()
    conn.close()

    return inserted


# ------------------------------------------------------------
#  Activity
# ------------------------------------------------------------
def insert_activity(count):
    conn = get_connection()
    cursor = conn.cursor()

    sql = """
        INSERT INTO activity (timestamp, count)
        VALUES (%s, %s)
    """

    cursor.execute(sql, (datetime.now(), count))
    conn.commit()

    cursor.close()
    conn.close()


# ------------------------------------------------------------
#  Pipeline logs
# ------------------------------------------------------------
def insert_pipeline_log(status, message, run_id):
    conn = get_connection()
    cursor = conn.cursor()

    sql = """
        INSERT INTO pipeline_logs (run_id, timestamp, status, message)
        VALUES (%s, %s, %s, %s)
    """

    cursor.execute(sql, (
        run_id,
        datetime.now(),
        status,
        message
    ))

    conn.commit()
    cursor.close()
    conn.close()


# ------------------------------------------------------------
#  Sources
# ------------------------------------------------------------
def get_source_id(url):
    conn = get_connection()
    cursor = conn.cursor()

    sql = "SELECT id FROM sources WHERE url = %s LIMIT 1"
    cursor.execute(sql, (url,))
    row = cursor.fetchone()

    cursor.close()
    conn.close()

    return row[0] if row else None


def get_all_sources():
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    sql = "SELECT id, url, section_id FROM sources ORDER BY section_id, id"
    cursor.execute(sql)
    rows = cursor.fetchall()

    cursor.close()
    conn.close()

    return rows


# ------------------------------------------------------------
#  Pipeline logs (par run_id)
# ------------------------------------------------------------
def get_pipeline_logs(run_id):
    conn = get_connection()
    cursor = conn.cursor(dictionary=True)

    cursor.execute(
        "SELECT status, message FROM pipeline_logs WHERE run_id = %s",
        (run_id,)
    )

    rows = cursor.fetchall()

    cursor.close()
    conn.close()

    return rows
