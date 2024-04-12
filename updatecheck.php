<?php
/**
 * WordPress-Plugin-Status-Update-Check
 * 
 * Dieses Skript führt den Befehl `wp plugin status` in einem WordPress-Verzeichnis aus und sendet die Ausgabe an einen Slack-Webhook.
 * 
 * @author Frank Neumann-Staude <frank@awesome.ug>
 * @version 1.0.0
 * 
 * All-Inkl.com erlaubt per Cron nur HTTP/S Abrufe.
 * Der Apache User bei all-inkl kann per exec die wp-cli Befehle nicht ausführen. Daher muss dieses Script 
 * von einem extenen Server per ssh aufgerufen werden.
 * 
 * ssh -f ssh-w012900a@w012900a.kasserver.com /usr/bin/php82 /www/htdocs/w012900a/energieausweis.de/updatecheck.php
 * 
 */

// Pfad zum WordPress-Unterverzeichnis
$wordpressDir = '/www/htdocs/w012900a/energieausweis.de/public/app';

// Slack Webhook URL
$slackWebhookUrl = 'https://hooks.slack.com/services/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';

// Befehl zum Ausführen
$command = 'wp plugin status';

chdir($wordpressDir);
$output = [];
$return_var = 0;
exec($command, $output, $return_var);

$outputString = implode("\n", $output);

$data = [
    'text' => "WP Plugin Status:\n" . $outputString,
];

$ch = curl_init($slackWebhookUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);

$result = curl_exec($ch);
curl_close($ch);
