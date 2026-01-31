import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, category, name, description, passes FROM features WHERE id = 27')
row = cursor.fetchone()
if row:
    print(f"ID: {row[0]}")
    print(f"Category: {row[1]}")
    print(f"Name: {row[2]}")
    print(f"Description: {row[3]}")
    print(f"Passes: {row[4]}")
else:
    print("Feature #27 not found")
conn.close()
