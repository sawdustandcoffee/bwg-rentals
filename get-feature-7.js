const db = require('node:sqlite');
const dbFile = './features.db';

const connection = new db.DatabaseSync(dbFile);
const stmt = connection.prepare('SELECT * FROM features WHERE id = ?');
const feature = stmt.get(7);
console.log(JSON.stringify(feature, null, 2));
connection.close();
