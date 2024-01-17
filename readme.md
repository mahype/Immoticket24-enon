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

Der Backupspace liegt bei Hetzner. Dazu gibt es einen Zugang zur Weboberfläche zur Administration und einen FTP Zugang zum Download der Backups.

Weboberfläche: https://robot.your-server.de/
FTP: Die Zugangsdaten sind im Passwortmanager hinterlegt.

## Deployment

Das Deployment erfolgt über Github Actions. 


