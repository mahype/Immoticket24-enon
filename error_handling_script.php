<?php

/**
 * Auto prepended file to handle errors.
 */
$slack_webhook_url = 'https://hooks.slack.com/services/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';

function slackErrorHandler($severity, $message, $file, $line) {
    $text = "Error: [$severity] $message in $file on line $line";
    sendSlackNotification($text);
    return false;
}

function slackExceptionHandler($exception) {
    $text = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    sendSlackNotification($text);
    return false;
}

function sendSlackNotification($text) {
    $data = ['text' => $text];
    $options = [
        'http' => [
            'header'  => "Content-type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data)
        ]
    ];
    $context = stream_context_create($options);
    file_get_contents($GLOBALS['slack_webhook_url'], false, $context);
}

set_error_handler("slackErrorHandler", E_ALL);
set_exception_handler("slackExceptionHandler");