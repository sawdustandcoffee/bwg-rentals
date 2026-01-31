import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()

cursor.execute('''
    SELECT id, name, passes, in_progress
    FROM features
    WHERE id = 61
''')

row = cursor.fetchone()
if row:
    print(f"Feature #{row[0]}: {row[1]}")
    print(f"  Passes: {bool(row[2])}")
    print(f"  In Progress: {bool(row[3])}")
    print(f"  Status: {'✅ PASSING' if row[2] else '❌ NOT PASSING'}")
else:
    print('Feature not found')

conn.close()
