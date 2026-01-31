#!/usr/bin/env node

const { DatabaseSync } = require('node:sqlite');
const db = new DatabaseSync('./features.db');
const stmt = db.prepare('SELECT id, name, passes FROM features WHERE id = 4');
const feature = stmt.get();
console.log(JSON.stringify(feature, null, 2));
db.close();
