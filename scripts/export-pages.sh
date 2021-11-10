#!/bin/sh
wp export --post_type=page --filename_format=pages.xml
wp menu export --all --filename=menu.json --allow-root