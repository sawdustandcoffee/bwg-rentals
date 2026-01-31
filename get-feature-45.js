#!/usr/bin/env node

const sqlite3 = require('better-sqlite3');
const db = new sqlite3('./features.db', { readonly: true });

try {
    const stmt = db.prepare('SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = ?');
    const feature = stmt.get(45);

    if (feature) {
        feature.steps = JSON.parse(feature.steps);
        feature.dependencies = JSON.parse(feature.dependencies);
        console.log(JSON.stringify(feature, null, 2));
    } else {
        console.log('Feature #45 not found');
    }
} catch (error) {
    console.error('Error:', error.message);
} finally {
    db.close();
}
