import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, name, passes FROM features WHERE id = 24')
row = cursor.fetchone()
if row:
    print(f"Feature #{row[0]}: {row[1]}")
    print(f"Passes: {'YES' if row[2] else 'NO'}")
else:
    print("Feature #24 not found")
conn.close()
