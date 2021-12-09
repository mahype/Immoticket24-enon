#!/bin/sh
wp export --post_type=page --filename_format=pages.xml
wp menu export --all --filename=menu.json --allow-root
wp gf form export --filename=forms.json