import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT * FROM features WHERE id = 31')
row = cursor.fetchone()
if row:
    columns = [desc[0] for desc in cursor.description]
    feature = dict(zip(columns, row))
    print(json.dumps(feature, indent=2))
else:
    print('Feature #31 not found')
conn.close()
