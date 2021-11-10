wp search-replace "enon.test" "2021.energiausweis-online-erstellen.de"
wp search-replace "wp.test" "2021.energieausweis-online-erstellen.de"
wp search-replace "wp-content/" "app/"
wp post delete $(wp post list --post_type=page --format=ids)