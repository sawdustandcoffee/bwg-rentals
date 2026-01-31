import sqlite3
import json

db = sqlite3.connect('features.db')
db.row_factory = sqlite3.Row
cursor = db.cursor()
cursor.execute("SELECT * FROM features WHERE id = 76")
feature = cursor.fetchone()
if feature:
    print(json.dumps(dict(feature), indent=2))
else:
    print("Feature not found")
