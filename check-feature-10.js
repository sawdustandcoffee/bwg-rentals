import { DatabaseSync } from 'node:sqlite';

const db = new DatabaseSync('features.db');
const query = db.prepare('SELECT id, name, passes FROM features WHERE id = ?');
const feature = query.get(10);

console.log('Feature #10 Status:');
console.log(JSON.stringify(feature, null, 2));

db.close();
