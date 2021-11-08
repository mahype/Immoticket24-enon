#!/bin/sh
wp post delete $(wp post list --post_type=page --format=ids)
wp import export-pages.xml 
wp search-replace "2021.energiausweis-online-erstellen.de" "enon.test"
wp search-replace "www.energiausweis-online-erstellen.de" "enon.test"
