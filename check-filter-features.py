import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('''
    SELECT id, name, passes, description
    FROM features 
    WHERE name LIKE '%filter%' OR description LIKE '%filter%'
    ORDER BY id
''')
rows = cursor.fetchall()
for row in rows:
    status = "✓ PASS" if row[2] else "✗ FAIL"
    print(f"#{row[0]} [{status}] {row[1]}")
    print(f"   {row[3][:80]}...")
    print()
conn.close()
