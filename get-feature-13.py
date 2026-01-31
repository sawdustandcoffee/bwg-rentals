import sqlite3
import json

db = sqlite3.connect('./features.db')
cursor = db.cursor()

cursor.execute('SELECT * FROM features WHERE id = ?', (13,))
columns = [description[0] for description in cursor.description]
row = cursor.fetchone()

if row:
    result = dict(zip(columns, row))
    print(json.dumps(result, indent=2))
else:
    print('Feature #13 not found')

db.close()
