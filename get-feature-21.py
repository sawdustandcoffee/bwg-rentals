#!/usr/bin/env python3
import sqlite3
conn = sqlite3.connect('features.db')
cur = conn.cursor()
cur.execute('SELECT * FROM features WHERE id = 21')
row = cur.fetchone()
if row:
    print(f"ID: {row[0]}")
    print(f"Priority: {row[1]}")
    print(f"Category: {row[2]}")
    print(f"Name: {row[3]}")
    print(f"Description: {row[4]}")
    print(f"Steps: {row[5]}")
    print(f"Passes: {row[6]}")
    print(f"In Progress: {row[7]}")
conn.close()
