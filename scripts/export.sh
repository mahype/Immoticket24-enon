#!/bin/sh
wp export --post_type=page --filename_format=pages.xml
wp export --post_type=post --filename_format=posts.xml
wp menu export --all --filename=menu.json --allow-root
wp gf form export --filename=forms.json