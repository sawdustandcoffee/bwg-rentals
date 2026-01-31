import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, name, passes, in_progress FROM features WHERE id = 30')
row = cursor.fetchone()
if row:
    print(f"Feature ID: {row[0]}")
    print(f"Name: {row[1]}")
    print(f"Passes: {row[2]} {'✅ PASSING' if row[2] == 1 else '❌ NOT PASSING'}")
    print(f"In Progress: {row[3]} {'⚠️ STILL IN PROGRESS' if row[3] == 1 else '✅ NOT IN PROGRESS'}")
    print("")
    if row[2] == 1 and row[3] == 0:
        print("STATUS: ✅ FEATURE #30 IS CORRECTLY MARKED AS PASSING")
    else:
        print("STATUS: ⚠️ FEATURE #30 STATUS NEEDS ATTENTION")
else:
    print("ERROR: Feature #30 not found")
conn.close()
