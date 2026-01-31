#!/bin/bash
sqlite3 features.db "SELECT id, category, name, description, steps FROM features WHERE id = 18"
