#!/usr/bin/env python3
import sqlite3
import json

db = sqlite3.connect('features.db')
cursor = db.cursor()
cursor.execute("SELECT * FROM features WHERE id = 54")
columns = [description[0] for description in cursor.description]
row = cursor.fetchone()

if row:
    feature = dict(zip(columns, row))
    print(json.dumps(feature, indent=2))
else:
    print("Feature #54 not found")

db.close()
