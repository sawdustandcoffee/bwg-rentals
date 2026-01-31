import sqlite3
conn = sqlite3.connect('features.db')
cursor = conn.cursor()
cursor.execute('SELECT id, name, passes, in_progress FROM features WHERE id = 20')
row = cursor.fetchone()
print(f"Feature #{row[0]}: {row[1]}")
print(f"  Passes: {bool(row[2])}")
print(f"  In Progress: {bool(row[3])}")
cursor.execute('SELECT COUNT(*) FROM features WHERE passes = 1')
passing = cursor.fetchone()[0]
cursor.execute('SELECT COUNT(*) FROM features')
total = cursor.fetchone()[0]
print(f"\nProject Progress:")
print(f"  Passing: {passing}/{total}")
print(f"  Percentage: {passing/total*100:.1f}%")
conn.close()
