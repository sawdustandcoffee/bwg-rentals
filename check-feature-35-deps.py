import sqlite3

conn = sqlite3.connect('features.db')
cursor = conn.cursor()

# Check Feature #4 (dependency)
cursor.execute("SELECT id, name, passes FROM features WHERE id = 4")
dep = cursor.fetchone()

if dep:
    print(f"Dependency Feature #{dep[0]}: {dep[1]}")
    print(f"Status: {'PASSING' if dep[2] == 1 else 'NOT PASSING'}")
else:
    print("Dependency Feature #4 not found")

conn.close()
