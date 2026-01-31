import sqlite3
conn = sqlite3.connect('features.db')
c = conn.cursor()
c.execute('SELECT id, name, description, passes FROM features WHERE id = 81')
row = c.fetchone()
if row:
    print(f"ID: {row[0]}")
    print(f"Name: {row[1]}")
    print(f"Description: {row[2]}")
    print(f"Passes: {row[3]}")
conn.close()
