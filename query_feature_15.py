#!/usr/bin/env python3
import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()

cursor.execute('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 15')
row = cursor.fetchone()

if row:
    print(f"Feature #{row[0]}:")
    print("=" * 50)
    print(f"Category: {row[1]}")
    print(f"Name: {row[2]}")
    print(f"Description: {row[3]}")
    print(f"Passes: {bool(row[5])}")
    print(f"In Progress: {bool(row[6])}")
    print(f"Dependencies: {row[7]}")
    print(f"\nSteps:")
    print(row[4])
else:
    print("Feature #15 not found")

conn.close()
