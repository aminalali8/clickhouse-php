<?php 

require 'vendor/autoload.php';

$_ENV['CH_HOST'] = 'clickhouse-5t7paj';

$config = [
    'host' => $_ENV['CH_HOST'],
    'port' => '8123',
    'username' => 'default',
    'password' => ''
];

$db = new ClickHouseDB\Client($config);
$db->database('default');
$db->setTimeout(1.5);      // 1500 ms
$db->setTimeout(10);       // 10 seconds
$db->setConnectTimeOut(5); // 5 seconds

print_r($db->showTables());

if (!$db->ping()) echo 'Error connect' else echo 'Connected!';