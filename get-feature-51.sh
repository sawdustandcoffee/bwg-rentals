#!/bin/bash
# Query features.db for Feature #51 details

echo "=== Feature #51 Details ==="
echo ""

# Using echo with pipe to avoid interactive mode
echo "SELECT id, category, name, description, steps, passes, in_progress, dependencies FROM features WHERE id = 51;" | sqlite3 features.db

echo ""
echo "=== Raw JSON Format ==="
echo "SELECT json_object('id', id, 'category', category, 'name', name, 'description', description, 'steps', steps, 'passes', passes, 'in_progress', in_progress, 'dependencies', dependencies) FROM features WHERE id = 51;" | sqlite3 features.db
