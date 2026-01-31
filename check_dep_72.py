import sqlite3
import json

db = sqlite3.connect('features.db')
db.row_factory = sqlite3.Row
cursor = db.cursor()
cursor.execute("SELECT id, name, passes FROM features WHERE id = 72")
feature = cursor.fetchone()
if feature:
    print(json.dumps(dict(feature), indent=2))
