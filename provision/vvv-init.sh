#!/usr/bin/env bash
# Provision WordPress Stable

DOMAIN=`get_primary_host "${VVV_SITE_NAME}".test`
DOMAINS=`get_hosts "${DOMAIN}"`
SITE_TITLE=`get_config_value 'site_title' "${DOMAIN}"`
WP_TYPE=`get_config_value 'wp_type' "single"`
DB_NAME=`get_config_value 'db_name' "${VVV_SITE_NAME}"`
DB_NAME=${DB_NAME//[\\\/\.\<\>\:\"\'\|\?\!\*]/}

# Make a database, if we don't already have one
echo -e "\nCreating database '${DB_NAME}' (if it's not already there)"
mysql -u root --password=root -e "CREATE DATABASE IF NOT EXISTS ${DB_NAME}"
mysql -u root --password=root -e "GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO wp@localhost IDENTIFIED BY 'wp';"
mysql -u root --password=root ${DB_NAME} < db.sql
echo -e "\n DB operations done.\n\n"

# Nginx Logs
mkdir -p ${VVV_PATH_TO_SITE}/log
touch ${VVV_PATH_TO_SITE}/log/error.log
touch ${VVV_PATH_TO_SITE}/log/access.log

# Install and configure the latest stable version of WordPress
if [[ ! -d "${VVV_PATH_TO_SITE}/public/core/wp-load.php" ]]; then
  echo "Downloading WordPress and dependencies..."
  cd ${VVV_PATH_TO_SITE}
  noroot composer install

  echo "Setting up local-config.php file..."
  cp -f "${VVV_PATH_TO_SITE}/provision/local-config.php.tmpl" "${VVV_PATH_TO_SITE}/local-config.php"
  sed -i "s#{{DB_NAME_HERE}}#${DB_NAME}#" "${VVV_PATH_TO_SITE}/local-config.php"
  sed -i "s#{{DOMAIN_HERE}}#${DOMAIN}#" "${VVV_PATH_TO_SITE}/local-config.php"

  echo "Setting up WordPress..."
  noroot wp search-replace 'energieausweis-online-erstellen.de' 'energieausweis-online-erstellen.test'
else
  echo "Updating WordPress and dependencies..."
  cd ${VVV_PATH_TO_SITE}
  noroot composer update
fi


echo "Addong node packages"
cd ${VVV_PATH_TO_SITE}/public/app/plugins/enon
npm install

cp -f "${VVV_PATH_TO_SITE}/provision/vvv-nginx.conf.tmpl" "${VVV_PATH_TO_SITE}/provision/vvv-nginx.conf"
sed -i "s#{{DOMAINS_HERE}}#${DOMAINS}#" "${VVV_PATH_TO_SITE}/provision/vvv-nginx.conf"

if [ -n "$(type -t is_utility_installed)" ] && [ "$(type -t is_utility_installed)" = function ] && `is_utility_installed core tls-ca`; then
    sed -i "s#{{TLS_CERT}}#ssl_certificate /vagrant/certificates/${VVV_SITE_NAME}/dev.crt;#" "${VVV_PATH_TO_SITE}/provision/vvv-nginx.conf"
    sed -i "s#{{TLS_KEY}}#ssl_certificate_key /vagrant/certificates/${VVV_SITE_NAME}/dev.key;#" "${VVV_PATH_TO_SITE}/provision/vvv-nginx.conf"
else
    sed -i "s#{{TLS_CERT}}##" "${VVV_PATH_TO_SITE}/provision/vvv-nginx.conf"
    sed -i "s#{{TLS_KEY}}##" "${VVV_PATH_TO_SITE}/provision/vvv-nginx.conf"
fi
