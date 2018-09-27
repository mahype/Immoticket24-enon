WP Energieausweis Online
========================

WP Energieausweis Online ist ein WordPress-Plugin, mit dem man in seine WordPress-Site die Möglichkeit einbauen kann, dass Kunden kostenpflichtig Energieausweise erstellen, indem sie ihre eigenen Daten eingeben und daraus automatische eine PDF-Datei erzeugt wird.

Der folgende Code sollte ganz oben in die `.htaccess`-Datei eingefügt werden:

```
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^edd-listener/([^/]+)/?$ /index.php?edd-listener=$1 [QSA,L]
</IfModule>
```
