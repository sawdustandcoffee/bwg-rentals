import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, name, passes FROM features WHERE id = 15')
row = cursor.fetchone()
print(f'Feature #15: {row[1]} - Passes: {row[2]}')
conn.close()
