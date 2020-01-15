#!/bin/bash

##
# Importing data from livesite
###############################

IMPORT_FILE=db.sql
DB_NAME=enon
DB_USER=root
DB_PASS=root

ONLINE_DOMAIN=energieausweis-online-erstellen.de
LOCAL_DOMAIN=enon.test

if [ -f "$IMPORT_FILE" ]; then
    mysql -u $DB_USER --password=$DB_PASS $DB_NAME < $IMPORT_FILE
	wp search-replace $ONLINE_DOMAIN $LOCAL_DOMAIN
	wp scrub posts --post_type=download --date='-3 month'
	echo "Finished!"
else
	echo "No db file found! Import failed."
fi




