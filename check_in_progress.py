import sqlite3
import json

db = sqlite3.connect('features.db')
cursor = db.cursor()
cursor.execute("SELECT id, name, category FROM features WHERE in_progress = 1 ORDER BY id")
features = []
for row in cursor.fetchall():
    features.append({"id": row[0], "name": row[1], "category": row[2]})

print("Features currently in progress:")
for f in features:
    print(f"  Feature #{f['id']}: [{f['category']}] {f['name']}")

db.close()
