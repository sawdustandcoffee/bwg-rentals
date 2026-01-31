import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('''
    SELECT id, category, name, description, steps, dependencies, passes, in_progress, priority
    FROM features
    WHERE id = 20
''')
row = cursor.fetchone()
if row:
    print(f"ID: {row[0]}")
    print(f"Category: {row[1]}")
    print(f"Name: {row[2]}")
    print(f"Description: {row[3]}")
    print(f"Steps: {row[4]}")
    print(f"Dependencies: {row[5]}")
    print(f"Passes: {row[6]}")
    print(f"In Progress: {row[7]}")
    print(f"Priority: {row[8]}")
conn.close()
