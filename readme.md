# Dokumentation energieausweis-online-erstellen.de

## Zugänge

### Webspace & Datenbank

Der Webspace als auch die Datenbanken werden über die [All Inkl](https://kas.all-inkl.com/) Oberfläche verwaltet. Die Zugangsdaten sind im Passwortmanager hinterlegt.

### FTP

Ein direkter Zugriff per FTP ist nicht möglich.

### SSH

Die SSH Zugänge werden über die All Inkl Oberfläche verwaltet. Dort werden auch die SSH Keys hinterlegt, um per SSH auf den Server zu kommen. Hierbei wird der Public Key hinterlegt und der Private Key muss lokal auf dem Rechner hinterlegt werden.

Der Zugang erfolgt nach erfolgreicher Einrichtung des Public Keys über:

```bash
ssh ssh-w012900a@energieausweis-online-erstellen.de
```

Ist alles korrekt eingerichtet, ist keine Passworteingabe mehr notwendig.

## Backup

Der Backupspace liegt bei Hetzner. Dazu gibt es einen Zugang zur Weboberfläche zur Administration und einen FTP Zugang zum Download der Backups. Die Zugangsdaten sind im Passwortmanager hinterlegt.

Weboberfläche: https://robot.your-server.de/

## Update der Webseite

Alle Plugins aus dem WordPress Repository werden über composer aktuell gehalten. Bei den Pro Plugins erfolgt das Update zunächst lokal und wird dann mit einem Commit pro upgedatetem Plugin in das Repository gepusht. 

In der lokalen Entwicklungsinstanz werden die möglichen Updates nicht angezeigt. 
Eine liste, welche Plugins aktulisierbar sind, kann per wp-cli angezeigt werden.
```bash
wp plugin status
```

Auch wenn über das Backend die Aktualisierungs-Funktion nicht verfügbar ist, können Plugins (und Themes) per wp-cli aktualisiert werden.

Beispiel für das Plugin shariff.
```bash
wp plugin update shariff
```
Be

Plugins, die nicht aus dem wordpress.org Repository verteilt werden, können unter umständen ( ein dritt Plugin  mit  identischen slug ist im wordpress.org Repository vorhanden ) melden das  Updates vorliegen, obwohl das Plugin aktuell ist und bei dem Versuche diese per wp-cli zu aktualisieren kommt die Fehlermeldung 
"version higher than expected".

IN KEINEM FALL darf das EDD Plugin aktualisiert werden. Allerdings muss das Plugin auf die neuesten Sicherheitslücken gecheckt werden und im Zweifel Bugfixes per Hand eingearbetet werden.

### Bisher berücksichte Sicherheitslücken in EDD

Liste der Sicherheitslücken in EDD:
https://www.wordfence.com/threat-intel/vulnerabilities/wordpress-plugins/easy-digital-downloads

Bisher berücksichtigte Sicherheitslücken:

- Easy Digital Downloads <= 3.2.5 - Authenticated (Contributor+) Stored Cross-Site Scripting
- Easy Digital Downloads <= 3.1.5 - Missing Authorization
- Easy Digital Downloads <= 3.1.1.4.2 - Cross-Site Request Forgery via edd_trigger_upgrades
- Easy Digital Downloads 3.1 - 3.1.1.4.1 - Unauthenticated Arbitrary Password Reset to Privilege Escalation
- Easy Digital Downloads <= 3.1.0.4 - Authenticated (Contributor+) Stored Cross-Site Scripting via Shortcode
- Easy Digital Downloads < 3.1.0.4 - SQL Injection
- Easy Digital Downloads <= 2.11.7 - Cross-Site Request Forgery to Arbitrary Post Deletion
- Easy Digital Downloads <= 3.1.0.1.1 - Unauthenticated CSV Injection
- Easy Digital Downloads <= 3.0.1 - PHP Object Injection

## Deployment

Das Deployment erfolgt über Github Actions jeweils über die entsprechenden Branches. Folgende Branches werden deployed:

- production - https://energieausweis-online-erstellen.de
- staging - https://staging.energieausweis-online-erstellen.de
- din18599 - https://din18599.energieausweis-online-erstellen.de

Hierbei wird der jeweilige Branch auf den Server deployed. Die Skripte dazu liegen in .github/workflows. Prinzipiell wird hier ein git pull gemacht und die composer depencies installiert.



