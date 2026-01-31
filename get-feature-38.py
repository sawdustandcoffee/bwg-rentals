import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()

cursor.execute('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 38')
row = cursor.fetchone()

if row:
    feature = {
        'id': row[0],
        'category': row[1],
        'name': row[2],
        'description': row[3],
        'steps': json.loads(row[4]),
        'passes': bool(row[5]),
        'in_progress': bool(row[6]),
        'dependencies': json.loads(row[7]) if row[7] else []
    }
    print(json.dumps(feature, indent=2))
else:
    print("Feature #38 not found")

conn.close()
