#!/bin/bash

# Import database via ddev
echo "> Importing database..."
ddev import-db --file=cleaned.sql
echo "> Database imported!"

# Install compoer dependencies via ddev
ddev composer install

# Install dependencies via
ddev npm install