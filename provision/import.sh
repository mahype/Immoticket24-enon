#!/bin/bash

##
# Importing data from livesite
###############################

FILE=db.sql

if [ -f "$FILE" ]; then
    mysql -u root --password=root enon < $FILE
	wp search-replace "energieausweis-online-erstellen.de" "enon.test"
	wp search-replace "www.energieausweis-online-erstellen.de" "enon.test"
	wp search-replace "https://enon.test" "http://enon.test"
	echo "Finished!"
else
	echo "No db file found! Import failed."
fi




