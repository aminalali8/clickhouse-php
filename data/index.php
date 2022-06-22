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

echo "<html><pre>";

if (!$db->ping()) {echo "</br>"; echo  'Error connect';} else { echo "</br>"; echo  " Connected!\n";};
// ---------------------------- Write ----------------------------
echo "</br>"; echo  "\n-----\ntry write:create_table\n";
$db->database('default');
//------------------------------------------------------------------------------

echo "</br>"; echo  'Tables EXISTS: ' . json_encode($db->showTables()) . PHP_EOL;
// $db->write('DROP TABLE IF EXISTS summing_url_views');
// echo "</br>"; echo  'Tables EXISTS: ' . json_encode($db->showTables()) . PHP_EOL;

$db->write('
    CREATE TABLE IF NOT EXISTS summing_url_views (
        event_date Date DEFAULT toDate(event_time),
        event_time DateTime,
        url_hash String,
        site_id Int32,
        views Int32,
        v_00 Int32,
        v_55 Int32
    ) 
    ENGINE = SummingMergeTree(event_date, (site_id, url_hash, event_time, event_date), 8192)
'
);
echo "</br>"; echo  'Table EXISTS: ' . json_encode($db->showTables()) . PHP_EOL;

/*
Table EXISTS: [{"name": "summing_url_views"}]
*/

//------------------------------------------------------------------------------
echo "</br>"; echo  "Insert\n";

$stat = $db->insert('summing_url_views',
    [
        [time(), 'HASH1', 2345, 22, 20, 2],
        [time(), 'HASH2', 2345, 12, 9, 3],
        [time(), 'HASH3', 5345, 33, 33, 0],
        [time(), 'HASH3', 5345, 55, 0, 55],
    ],
    ['event_time', 'url_hash', 'site_id', 'views', 'v_00', 'v_55']
);

echo "</br>"; echo  "Insert Done\n";
//------------------------------------------------------------------------------



echo "</br>"; echo  "Try select \n";


$st = $db->select('SELECT * FROM summing_url_views');




echo "</br>"; echo  "Count select rows:".$st->count()."\n";
echo "</br>"; echo  "Count all rows:".$st->countAll()."\n";
echo "</br>"; echo  "First row:\n";
print_r($st->fetchOne());

echo "</br>"; echo  "extremes_min:\n";
print_r($st->extremesMin());

echo "</br>"; echo  "totals:\n";
print_r($st->totals());



$st=$db->select('SELECT event_date,url_hash,sum(views),avg(views) FROM summing_url_views WHERE site_id<3333 GROUP BY event_date,url_hash WITH TOTALS');




echo "</br>"; echo  "Count select rows:".$st->count()."\n";
/*
2
 */
echo "</br>"; echo  "Count all rows:".$st->countAll()."\n";
/*
false
 */



echo "</br>"; echo  "First row:\n";
print_r($st->fetchOne());
/*
(
    [event_date] => 2016-07-18
    [url_hash] => HASH1
    [sum(views)] => 22
    [avg(views)] => 22
)
 */


echo "</br>"; echo  "totals:\n";
print_r($st->totals());
/*
(
    [event_date] => 0000-00-00
    [url_hash] =>
    [sum(views)] => 34
    [avg(views)] => 17
)

 */


echo "</br>"; echo  "Tree Path [event_date.url_hash]:\n";
print_r($st->rowsAsTree('event_date.url_hash'));
/*
(
    [2016-07-18] => Array
        (
            [HASH2] => Array
                (
                    [event_date] => 2016-07-18
                    [url_hash] => HASH2
                    [sum(views)] => 12
                    [avg(views)] => 12
                )
            [HASH1] => Array
                (
                    [event_date] => 2016-07-18
                    [url_hash] => HASH1
                    [sum(views)] => 22
                    [avg(views)] => 22
                )
        )
)
 */
// $db->write("DROP TABLE IF EXISTS summing_url_views");
// echo "</br>"; echo  "Tables EXISTS:".json_encode($db->showTables())."\n";
/*
Tables EXISTS:[]
 */
echo "Goodbye!";
echo "</pre></html>";



