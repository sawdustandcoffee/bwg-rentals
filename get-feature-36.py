import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, category, name, description, steps, dependencies FROM features WHERE id = 36')
row = cursor.fetchone()

if row:
    print(f'ID: {row[0]}')
    print(f'Category: {row[1]}')
    print(f'Name: {row[2]}')
    print(f'Description: {row[3]}')
    print(f'\nSteps:')
    steps = json.loads(row[4])
    for i, step in enumerate(steps, 1):
        print(f'  {i}. {step}')
    print(f'\nDependencies: {row[5]}')
else:
    print('Feature not found')

conn.close()
