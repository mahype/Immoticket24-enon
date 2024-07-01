<?php

/**
 * Auto prepended file to handle errors.
 */
$slack_webhook_url = 'https://hooks.slack.com/services/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';

function getErrorType($type) {
    $error_types = [
        E_ERROR             => 'E_ERROR',
        E_WARNING           => 'E_WARNING',
        E_PARSE             => 'E_PARSE',
        E_NOTICE            => 'E_NOTICE',
        E_CORE_ERROR        => 'E_CORE_ERROR',
        E_CORE_WARNING      => 'E_CORE_WARNING',
        E_COMPILE_ERROR     => 'E_COMPILE_ERROR',
        E_COMPILE_WARNING   => 'E_COMPILE_WARNING',
        E_USER_ERROR        => 'E_USER_ERROR',
        E_USER_WARNING      => 'E_USER_WARNING',
        E_USER_NOTICE       => 'E_USER_NOTICE',
        E_STRICT            => 'E_STRICT',
        E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
        E_DEPRECATED        => 'E_DEPRECATED',
        E_USER_DEPRECATED   => 'E_USER_DEPRECATED',
    ];

    return $error_types[$type] ?? 'UNKNOWN_ERROR';
}

function slackErrorHandler($severity, $message, $file, $line) {
    $severity = getErrorType($severity);
    
    $text = "Error: [$severity] $message in $file on line $line";
    $text .= "\nURL: " . $_SERVER['REQUEST_URI'];
    
    if (!empty($_POST)) {
        $text .= "\nPOST Variables: " . print_r($_POST, true);
        $text .= "\nPOST URL: " . $_SERVER['HTTP_REFERER'];
    }

    sendSlackNotification("```" . $text . "```");
    return false;
}

function slackExceptionHandler($exception) {
    $text = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
    $text .= "\nURL: " . $_SERVER['REQUEST_URI'];

    if (!empty($_POST)) {
        $text .= "\nPOST Variables: " . print_r($_POST, true);
        $text .= "\nPOST URL: " . $_SERVER['HTTP_REFERER'];
    }

    sendSlackNotification("```" . $text . "```");
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

function shutdownHandler() {
    $last_error = error_get_last();

    $logged_error_types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING, E_RECOVERABLE_ERROR];

    if ( $last_error && in_array($last_error['type'], $logged_error_types) ){
        slackErrorHandler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
    }
}

register_shutdown_function('shutdownHandler');
// set_error_handler("slackErrorHandler", E_ERROR); // Handled by shutdownHandler
set_exception_handler("slackExceptionHandler");

// Test
// throw new Exception('Test exception');
// trigger_error('Test error', E_USER_ERROR);