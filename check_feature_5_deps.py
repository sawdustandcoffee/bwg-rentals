import sqlite3
db = sqlite3.connect('/home/buckneri/projects/bwg-rentals/features.db')
cursor = db.cursor()

# Check Feature #5
cursor.execute("SELECT id, name, description, passes, in_progress, dependencies FROM features WHERE id = 5")
f5 = cursor.fetchone()
print("Feature #5:")
print(f"  Name: {f5[1]}")
print(f"  Description: {f5[2]}")
print(f"  Passes: {f5[3]}")
print(f"  In Progress: {f5[4]}")
print(f"  Dependencies: {f5[5]}")

# Check dependency (Feature #4)
if f5[5]:
    import json
    deps = json.loads(f5[5])
    for dep_id in deps:
        cursor.execute("SELECT id, name, passes FROM features WHERE id = ?", (dep_id,))
        dep = cursor.fetchone()
        print(f"\nDependency Feature #{dep[0]}:")
        print(f"  Name: {dep[1]}")
        print(f"  Passes: {dep[2]}")

db.close()
