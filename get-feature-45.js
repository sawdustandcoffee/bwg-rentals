#!/usr/bin/env node

const fs = require('fs');
const { DatabaseSync } = require('node:sqlite');

try {
    const db = new DatabaseSync('./features.db');

    const query = 'SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 45';
    const stmt = db.prepare(query);
    const feature = stmt.get();

    if (feature) {
        feature.steps = JSON.parse(feature.steps);
        feature.dependencies = JSON.parse(feature.dependencies);
        console.log(JSON.stringify(feature, null, 2));
    } else {
        console.log('Feature #45 not found');
    }

    db.close();
} catch (error) {
    console.error('Error:', error.message);
    console.error(error.stack);
}
