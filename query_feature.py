import sqlite3
import json

db = sqlite3.connect('features.db')
cursor = db.cursor()
cursor.execute("SELECT * FROM features WHERE id = 64")
columns = [description[0] for description in cursor.description]
row = cursor.fetchone()
if row:
    feature = dict(zip(columns, row))
    # Parse JSON fields
    if feature.get('steps'):
        feature['steps'] = json.loads(feature['steps'])
    if feature.get('dependencies'):
        feature['dependencies'] = json.loads(feature['dependencies'])
    print(json.dumps(feature, indent=2))
else:
    print("Feature #64 not found")
db.close()
