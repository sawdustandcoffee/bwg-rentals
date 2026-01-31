import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()

# Get Feature #20
cursor.execute('SELECT id, name, description, steps, passes, in_progress FROM features WHERE id = 20')
row = cursor.fetchone()
if row:
    print(f"Feature #20 Status:")
    print(f"  Name: {row[1]}")
    print(f"  Description: {row[2]}")
    print(f"  Passes: {row[4]}")
    print(f"  In Progress: {row[5]}")
    print(f"  Steps: {row[3]}")

conn.close()
