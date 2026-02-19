# db.py
import mysql.connector
from mysql.connector import Error
from datetime import datetime


# ------------------------------------------------------------
#  Connexion
# ------------------------------------------------------------
def get_connection():
    """
    Retourne une connexion MySQL.
    Adaptée pour Windows + Poetry + pipeline GitHub Actions.
    """
    return mysql.connector.connect(
        host="localhost",
        user="root",
        password="",
        database="veille_local",
        charset="utf8mb4"
    )


# ------------------------------------------------------------
#  Articles
# ------------------------------------------------------------
def insert_article(article):
    """
    Insère un article si son URL n'existe pas déjà.
    article = {
        "title": str,
        "url": str,
        "published_at": datetime,
        "source_id": int
    }
    Retourne 1 si ajouté, 0 si doublon.
    """
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
#  Activity (nombre d’articles ajoutés)
# ------------------------------------------------------------
def insert_activity(count):
    """
    Insère une entrée dans activity :
    count = nombre d’articles ajoutés lors de cette exécution.
    """
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
from datetime import datetime

def insert_pipeline_log(status, message, run_id):
    """
    Insère un log dans pipeline_logs.
    - status : "OK", "ERROR", "WARN", "INFO"
    - message : texte libre
    - run_id : identifiant unique du pipeline (int)
    """
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
    """
    Retourne l'id de la source correspondant à l'URL du flux RSS.
    """
    conn = get_connection()
    cursor = conn.cursor()

    sql = "SELECT id FROM sources WHERE url = %s LIMIT 1"
    cursor.execute(sql, (url,))
    row = cursor.fetchone()

    cursor.close()
    conn.close()

    return row[0] if row else None

# ------------------------------------------------------------
#  Sources (toutes)
# ------------------------------------------------------------
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
    conn = get_connection()  # ← utilise ta fonction existante
    cursor = conn.cursor(dictionary=True)
    cursor.execute(
        "SELECT status, message FROM pipeline_logs WHERE run_id = %s",
        (run_id,)
    )
    return cursor.fetchall()
