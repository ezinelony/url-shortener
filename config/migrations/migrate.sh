#!/bin/bash

echo "Creating Relations..."

sqlite3 ./config/data/database.db  <  ./config/migrations/schema.sql

echo "Done Bye..."