#!/bin/bash
python3 << 'PYTHON_SCRIPT'
import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT * FROM features WHERE id = 70')
feature = cursor.fetchone()

if feature:
    print(f'ID: {feature[0]}')
    print(f'Priority: {feature[1]}')
    print(f'Category: {feature[2]}')
    print(f'Name: {feature[3]}')
    print(f'Description: {feature[4]}')
    print(f'Steps:')
    steps = json.loads(feature[5])
    for i, step in enumerate(steps, 1):
        print(f'  {i}. {step}')
    print(f'Passes: {feature[6]}')
    print(f'In Progress: {feature[7]}')
    if len(feature) > 8 and feature[8]:
        print(f'Dependencies: {feature[8]}')
else:
    print('Feature #70 not found')

conn.close()
PYTHON_SCRIPT
