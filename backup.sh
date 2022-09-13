#!/bin/bash

#### Konfiguration des Backupscripts ####
PFAD=/Backup_Extern
BACKUP_ORDNER=$PFAD/backups/
NOW=$(date +"%Y-%m-%d-%H%M")

# Konfiguration der WordPress-Seite #
DB_NAME='d01c4312'
DB_USER='d01c4312'
DB_PASS='qfEE7woSrKPxYdLF'
DB_HOST='localhost'

# Datenbank Backup #
function database_backup {
    mysqldump -h $DB_HOST -u $DB_USER -p$DB_PASS $DB_NAME > $PFAD/$DB_NAME.$NOW.sql
}
# Runner #
database_backup
echo "Datenbank-Dump erfolgeich erstellt."
sleep 5
echo "Datenbank-Backup wird jetzt auf Backup-Storage geladen."
rsync -a --progress -e 'ssh -i /www/htdocs/w012900a/bin/.ssh/backup_storage_key -p23' --recursive $PFAD u313820@u313820.your-storagebox.de:enon_db/
echo "Datenbank-Dump erfolgreich auf Backup-Storage geladen."
sleep 5
echo "Lokaler Datenbank-Dump wird gelöscht"
sleep 1
rm -R $PFAD/*
echo "Lokaler Datenbank-Dump wurde gelöscht"
sleep 1
echo "Synchronisierung der WordPress-Dateien beginnt."
rsync -a --progress -e 'ssh -i /www/htdocs/w012900a/bin/.ssh/backup_storage_key -p23' --recursive /www/htdocs/w012900a/production.energieausweis-online-erstellen.de u195082-sub1@u195082-sub1.your-storagebox.de:enon_web/
sleep 1
echo "Synchronisierung der WordPress-Dateien beendet."