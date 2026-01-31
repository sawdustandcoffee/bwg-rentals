import sqlite3
db = sqlite3.connect('/home/buckneri/projects/bwg-rentals/features.db')
cursor = db.cursor()
cursor.execute("SELECT id, priority, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 5")
result = cursor.fetchone()
if result:
    print(f"ID: {result[0]}")
    print(f"Priority: {result[1]}")
    print(f"Category: {result[2]}")
    print(f"Name: {result[3]}")
    print(f"Description: {result[4]}")
    print(f"Steps: {result[5]}")
    print(f"Passes: {result[6]}")
    print(f"In Progress: {result[7]}")
    print(f"Dependencies: {result[8]}")
else:
    print("Feature not found")
db.close()
