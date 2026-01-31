import { DatabaseSync } from 'node:sqlite';

const db = new DatabaseSync('features.db');
const query = db.prepare('SELECT * FROM features WHERE id = ?');
const feature = query.get(18);

console.log(JSON.stringify(feature, null, 2));

db.close();
