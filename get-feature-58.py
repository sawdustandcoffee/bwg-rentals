#!/usr/bin/env python3
import json

with open('features.db', 'r') as f:
    data = json.load(f)

feature = [f for f in data['features'] if f['id'] == 58]
if feature:
    print(json.dumps(feature[0], indent=2))
else:
    print("Feature #58 not found")
