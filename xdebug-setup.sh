#!/bin/bash

# Ermitteln der lokalen IP-Adresse auf macOS
# Dies wählt die erste IP-Adresse aus, die nicht 127.0.0.1 ist
IP_ADDRESS=$(ifconfig | grep 'inet ' | grep -v '127.0.0.1' | awk '{print $2}' | head -n 1)

# Pfad zur xdebug.ini-Datei
XDEBUG_INI_PATH=".ddev/php/xdebug.ini"

# Überprüfen, ob die xdebug.ini-Datei existiert, andernfalls erstellen
if [ ! -f "$XDEBUG_INI_PATH" ]; then
    touch "$XDEBUG_INI_PATH"
fi

# Wenn nicht vorhanden gebe den Pfad zur xdebug logdatei an.
if ! grep -q "xdebug.log" "$XDEBUG_INI_PATH"; then
    echo "xdebug.log=/var/log/xdebug.log" >> $XDEBUG_INI_PATH
fi

# Überprüfen, ob die xdebug.client_host-Einstellung in der xdebug.ini-Datei vorhanden ist
if ! grep -q "xdebug.client_host" "$XDEBUG_INI_PATH"; then
    echo "xdebug.client_host=$IP_ADDRESS" >> $XDEBUG_INI_PATH    
else
    # Aktualisieren der xdebug.client_host-Einstellung in der xdebug.ini-Datei
    # macOS benötigt ein leeres Argument nach -i bei sed, um eine Sicherungskopie ohne Suffix zu vermeiden
    sed -i '' "s/xdebug.client_host=.*/xdebug.client_host=$IP_ADDRESS/" $XDEBUG_INI_PATH
fi


echo "xdebug.client_host wurde auf $IP_ADDRESS aktualisiert."

ddev xdebug on

# DDEV neu starten
ddev restart