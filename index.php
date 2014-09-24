<?php

$configPath = __DIR__ . '/config/config.php';

if (file_exists($configPath)) {
    $config = require_once($configPath);
} else {
    $config = [
        'fallBackUrl' => 'http://google.com',
        'actions'     => []
    ];
}

$pathParts = explode('/', $_SERVER['PHP_SELF']);

if (count($pathParts) === 2) {
    $action = '';
} else {
    $action = end($pathParts);
}

$params = '';

$url = $config['fallBackUrl'];

foreach ($config['actions'] as $actionConfig) {
    if ($actionConfig['action'] == $action) {

        $params = http_build_query($_GET);

        $url = sprintf('%s%s%s', $actionConfig['forward'], $params ? '?' : '', $params);

        break;
    }
}

$stats = [
    date('y-m-d H:i:s'),
    rand(10000, 99999),
    $action,
    $params,
    $url
];

$content = implode(';', $stats) . PHP_EOL;

file_put_contents(__DIR__ . '/stats/stats.csv', $content, FILE_APPEND);

header("HTTP/1.1 301 Moved Permanently");
header("location: " . $url);
