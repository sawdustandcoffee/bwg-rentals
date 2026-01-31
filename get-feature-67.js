const db = require('node:sqlite');
const dbConn = new db.DatabaseSync('features.db');
const row = dbConn.prepare('SELECT * FROM features WHERE id = 67').get();
console.log(JSON.stringify(row, null, 2));
dbConn.close();
