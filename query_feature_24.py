import sqlite3
conn = sqlite3.connect('features.db')
c = conn.cursor()
c.execute('SELECT * FROM features WHERE id = 24')
row = c.fetchone()
if row:
    cols = [d[0] for d in c.description]
    feature = dict(zip(cols, row))
    print(f"ID: {feature['id']}")
    print(f"Category: {feature['category']}")
    print(f"Name: {feature['name']}")
    print(f"Description: {feature['description']}")
    print(f"Passes: {feature['passes']}")
    print(f"In Progress: {feature['in_progress']}")
    print(f"Steps: {feature['steps']}")
conn.close()
