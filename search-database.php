<?php

use src\Database;
use src\Logger;

require '_bootstrap.php';

$db = new Database();
$logger = new Logger();

$search = 'price';
$results = $db->searchAllDb($search);

$logger->log('');
$logger->log('########################');
$logger->log('#### Search results ####');
$logger->log('#######################');
$logger->log('');

foreach ($results as $item){
    $logger->log('#### ' . $item['table'] . ' ####');
    $logger->log($item['sql']);
    $logger->log('Count:  ' .$item['count']);
    $logger->log('');
}
