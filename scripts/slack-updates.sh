#!/bin/bash

# Konfiguration
SLACK_WEBHOOK_URL="https://hooks.slack.com/services/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"
WP_PATH="$(cd ../ && cd public && pwd)"

# Überprüfen Sie, ob WP CLI installiert ist
if ! command -v wp &> /dev/null
then
    echo "WP CLI konnte nicht gefunden werden. Bitte installieren Sie WP CLI."
    exit 1
fi

# Wechseln Sie in das WordPress-Verzeichnis
cd $WP_PATH

# Erhalten Sie die Liste der verfügbaren Updates
WP_UPDATES=$(wp core check-update --format=json)
PLUGIN_UPDATES=$(wp plugin list --update=available --format=json)
THEME_UPDATES=$(wp theme list --update=available --format=json)

# Erstellen Sie die Nachricht für Slack
SLACK_MESSAGE="*WordPress-Updates verfügbar:*\n"
SLACK_MESSAGE+="*Core Updates:*\n$WP_UPDATES\n"
SLACK_MESSAGE+="*Plugin Updates:*\n$PLUGIN_UPDATES\n"
SLACK_MESSAGE+="*Theme Updates:*\n$THEME_UPDATES"

echo $SLACK_MESSAGE

# Senden Sie die Nachricht an Slack
curl -X POST -H 'Content-type: application/json' --data "{\"text\": \"${SLACK_MESSAGE}\"}" $SLACK_WEBHOOK_URL
