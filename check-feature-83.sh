#!/bin/bash
curl -s -X POST http://localhost:8088/wp-admin/admin-ajax.php \
  -d "action=get_feature" \
  -d "feature_id=83" 2>&1 | grep -A5 "Feature"
