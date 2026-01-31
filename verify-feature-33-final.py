import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, name, passes, in_progress FROM features WHERE id = 33')
row = cursor.fetchone()
if row:
    print(f"Feature #{row[0]}: {row[1]}")
    print(f"Passes: {row[2]}")
    print(f"In Progress: {row[3]}")
    print(f"\nStatus: {'✅ PASSING' if row[2] else '❌ NOT PASSING'}")
else:
    print("Feature not found")
conn.close()
