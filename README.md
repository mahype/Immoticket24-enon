# Energieausweis-Online-Erstellen.de Network

This project is for development of the Energieausweis-Online-Erstellen.de Network.

## Setup for VVV

Add the following to the `sites` section of your `vvv-custom.yml`. If you don't have this file, duplicate `vvv-config.yml` and rename it accordingly.

```
energieausweis-online-erstellen:
  repo: git@github.com:mahype/energieausweis-online-erstellen-network.git
  hosts:
    - energieausweis-online-erstellen.test
  custom:
    db_name: energieausweis_online_erstellen
```

After setting this up, start your VVV instance and call `vagrant provision --provision-with site-energieausweis-online-erstellen`.

You can then access the API-API console via `https://energieausweis-online-erstellen.test` in your browser.

## Good to Know

* This repository is not connected to the website. Any changes need to be manually uploaded via FTP.
* Plugins can be updated via the admin on energieausweis-online-erstellen.de. Make sure to keep the versions in `composer.json` in sync with the actual versions on the live website.
* The following premium plugins cannot be updated via `composer.json` although they can be updated via the admin. They are therefore directly included in the repository.
    * `affiliate-wp`
    * `affiliate-wp-lifetime-commissions`
    * `optimizePressPlugin`
    * `optimizePressPlusPack`
    * `wordpress-seo-premium`
* Easy Digital Downloads requires one minor change in order to work correctly with WP Energieausweis Online. This change is automatically applied on `composer update` through a patch file, but when updating the plugin on the website, it needs to be put there manually.
* The following snippet must be part of `.htaccess`:
```
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^edd-listener/([^/]+)/?$ /index.php?edd-listener=$1 [QSA,L]
</IfModule>
```
