#!/bin/sh
wp import pages.xml --authors=skip
wp menu import menu.json --allow-root
wp option update show_on_front 'page'
wp option update page_on_front 465251