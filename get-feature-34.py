import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, category, name, description, steps, passes, in_progress FROM features WHERE id = 34')
result = cursor.fetchone()
if result:
    print(f'ID: {result[0]}')
    print(f'Category: {result[1]}')
    print(f'Name: {result[2]}')
    print(f'Description: {result[3]}')
    print(f'Steps: {result[4]}')
    print(f'Passes: {result[5]}')
    print(f'In Progress: {result[6]}')
conn.close()
