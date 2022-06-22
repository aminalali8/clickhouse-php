<?php 

require 'vendor/autoload.php';

$_ENV['CH_HOST'] = 'clickhouse-zhng4w';

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

$db->write('
    CREATE TABLE IF NOT EXISTS summing_url_views (
        event_date Date DEFAULT toDate(event_time),
        event_time DateTime,
        site_id Int32,
        site_key String,
        views Int32,
        v_00 Int32,
        v_55 Int32
    )
    ENGINE = SummingMergeTree(event_date, (site_id, site_key, event_time, event_date), 8192)
');

print_r($db->showTables());
echo $db->showCreateTable('summing_url_views');
$stat = $db->insert('summing_url_views',
    [
        [time(), 'HASH1', 2345, 22, 20, 2],
        [time(), 'HASH2', 2345, 12, 9,  3],
        [time(), 'HASH3', 5345, 33, 33, 0],
        [time(), 'HASH3', 5345, 55, 0, 55],
    ],
    ['event_time', 'site_key', 'site_id', 'views', 'v_00', 'v_55']
);

$statement = $db->select('SELECT * FROM summing_url_views LIMIT 3');

// Count select rows
$statement->count();

// Count all rows
$statement->countAll();

// fetch one row
$statement->fetchOne();

// get extremes min
print_r($statement->extremesMin());

// totals row
print_r($statement->totals());

// result all
print_r($statement->rows());

// totalTimeRequest
print_r($statement->totalTimeRequest());

// raw answer JsonDecode array, for economy memory
print_r($statement->rawData());

// raw curl_info answer
print_r($statement->responseInfo());

// human size info
print_r($statement->info());

// if clickhouse-server version >= 54011
$db->settings()->set('output_format_write_statistics',true);
print_r($statement->statistics());

$statement = $db->select('
    SELECT event_date, site_key, sum(views), avg(views)
    FROM summing_url_views
    WHERE site_id < 3333
    GROUP BY event_date, url_hash
    WITH TOTALS
');

print_r($statement->rowsAsTree('event_date.site_key'));

$db->write('DROP TABLE IF EXISTS summing_url_views');
echo " \n deleted database;"


if (!$db->ping()) {echo 'Error connect';} else { echo "\n Connected!";};