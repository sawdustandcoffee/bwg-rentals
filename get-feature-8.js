const fs = require('fs');
const Database = require('node:sqlite');

async function getFeature8() {
    const db = new Database.DatabaseSync('./features.db');

    try {
        const stmt = db.prepare('SELECT * FROM features WHERE id = ?');
        const feature = stmt.get(8);

        if (feature) {
            console.log(JSON.stringify(feature, null, 2));
        } else {
            console.log('Feature #8 not found');
        }
    } catch (error) {
        console.error('Error:', error.message);
    } finally {
        db.close();
    }
}

getFeature8();
