<?php

$configPath = __DIR__ . '/../config/config.php';

if (file_exists($configPath)) {
    $config = require_once($configPath);
} else {
    $config = [
        'fallBackUrl' => 'http://google.com',
        'actions'     => []
    ];
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
} else {
    $referer = '';
}

$path   = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
$action = str_replace($path, '', $_SERVER['REQUEST_URI']);

$pathParts = explode('?', $action);

if (count($pathParts) <= 2) {
    $action = $pathParts[0];
} else {
    die('wrong url with to many ?s');
}

$params = http_build_query($_GET);

$url = $config['fallBackUrl'];

foreach ($config['actions'] as $actionConfig) {
    if ($actionConfig['action'] == $action) {
        $url = sprintf('%s%s%s', $actionConfig['forward'], $params ? '?' : '', $params);
        break;
    }
}

$stats = [
    date('y-m-d H:i:s'),
    rand(10000, 99999),
    $action,
    $params,
    $url,
    $referer
];

$content = implode(';', $stats) . PHP_EOL;

file_put_contents(__DIR__ . '/../stats/stats.csv', $content, FILE_APPEND);

header("HTTP/1.1 301 Moved Permanently");
header("location: " . $url);
