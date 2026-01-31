#!/usr/bin/env python3
import sqlite3
import json

db = sqlite3.connect('features.db')
db.row_factory = sqlite3.Row
cursor = db.cursor()

cursor.execute('SELECT * FROM features WHERE id = 87')
row = cursor.fetchone()

if row:
    feature = dict(row)
    print(json.dumps(feature, indent=2))
else:
    print('Feature #87 not found')

db.close()
