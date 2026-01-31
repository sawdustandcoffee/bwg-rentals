import sqlite3
db = sqlite3.connect('/home/buckneri/projects/bwg-rentals/features.db')
cursor = db.cursor()
cursor.execute("SELECT id, name, passes, in_progress FROM features WHERE id = 5")
result = cursor.fetchone()
print(f"Feature #{result[0]}: {result[1]}")
print(f"  Passes: {bool(result[2])}")
print(f"  In Progress: {bool(result[3])}")
db.close()
