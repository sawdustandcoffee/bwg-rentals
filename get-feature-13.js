const sqlite3 = require('sqlite3').verbose();

const db = new sqlite3.Database('./features.db', (err) => {
  if (err) {
    console.error('Error opening database:', err.message);
    process.exit(1);
  }
});

db.get('SELECT * FROM features WHERE id = ?', [13], (err, row) => {
  if (err) {
    console.error('Error:', err.message);
    process.exit(1);
  }
  console.log(JSON.stringify(row, null, 2));
  db.close();
});
