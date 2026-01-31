#!/usr/bin/env python3
import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()

cursor.execute("SELECT id, name, passes FROM features WHERE id = 43")
row = cursor.fetchone()
if row:
    print(f"Feature #{row[0]}: {row[1]}")
    print(f"Status: {'PASSING' if row[2] else 'NOT PASSING'}")
else:
    print("Feature 43 not found")

conn.close()
