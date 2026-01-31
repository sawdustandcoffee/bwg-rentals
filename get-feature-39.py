#!/usr/bin/env python3
import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, priority, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 39')
feature = cursor.fetchone()

if feature:
    print(f'ID: {feature[0]}')
    print(f'Priority: {feature[1]}')
    print(f'Category: {feature[2]}')
    print(f'Name: {feature[3]}')
    print(f'Description: {feature[4]}')
    print(f'\nSteps:')
    steps = json.loads(feature[5])
    for i, step in enumerate(steps, 1):
        print(f'  {i}. {step}')
    print(f'\nPasses: {feature[6]}')
    print(f'In Progress: {feature[7]}')
    print(f'Dependencies: {feature[8]}')
else:
    print('Feature #39 not found')

conn.close()
