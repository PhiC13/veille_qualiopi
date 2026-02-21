import sys
import os

# Ajouter src/ au PYTHONPATH
BASE_DIR = os.path.dirname(os.path.abspath(__file__))
SRC_DIR = os.path.join(BASE_DIR, "src")
sys.path.insert(0, SRC_DIR)

from rss_qualiopi.aggregate import run_full_import

if __name__ == "__main__":
    run_full_import()
