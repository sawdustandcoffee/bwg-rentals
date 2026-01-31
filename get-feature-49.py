import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT * FROM features WHERE id = 49')
feature = cursor.fetchone()
if feature:
    cols = [d[0] for d in cursor.description]
    for col, val in zip(cols, feature):
        print(f"{col}: {val}")
conn.close()
