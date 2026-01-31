const Database = require('node:sqlite');
const db = new Database.DatabaseSync('features.db');
const stmt = db.prepare('SELECT * FROM features WHERE id = ?');
const feature = stmt.get(47);
console.log(JSON.stringify(feature, null, 2));
db.close();
