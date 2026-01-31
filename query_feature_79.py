import sqlite3
import json

conn = sqlite3.connect('features.db')
cursor = conn.cursor()

cursor.execute('''
    SELECT id, category, name, description, steps, passes, in_progress, dependencies
    FROM features
    WHERE id = 79
''')

result = cursor.fetchone()

if result:
    feature = {
        'id': result[0],
        'category': result[1],
        'name': result[2],
        'description': result[3],
        'steps': json.loads(result[4]) if result[4] else [],
        'passes': result[5],
        'in_progress': result[6],
        'dependencies': result[7]
    }
    print(json.dumps(feature, indent=2))
else:
    print("Feature #79 not found")

conn.close()
