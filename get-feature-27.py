import sqlite3

conn = sqlite3.connect('features.db')
cursor = conn.cursor()

cursor.execute('''
    SELECT id, name, passes, in_progress
    FROM features 
    WHERE id = 27
''')

row = cursor.fetchone()
if row:
    print(f"Feature #{row[0]}: {row[1]}")
    print(f"Status: {'✅ PASSING' if row[2] else '❌ PENDING'}")
    print(f"In Progress: {'Yes' if row[3] else 'No'}")
else:
    print("Feature not found")

conn.close()
