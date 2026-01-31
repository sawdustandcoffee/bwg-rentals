import sqlite3
import json

conn = sqlite3.connect('/home/buckneri/projects/bwg-rentals/features.db')
cursor = conn.cursor()

cursor.execute('SELECT * FROM features WHERE id = 32')
row = cursor.fetchone()

if row:
    feature = {
        'id': row[0],
        'priority': row[1],
        'category': row[2],
        'name': row[3],
        'description': row[4],
        'steps': json.loads(row[5]),
        'passes': bool(row[6]),
        'in_progress': bool(row[7])
    }
    print(json.dumps(feature, indent=2))
else:
    print("Feature #32 not found")

conn.close()
