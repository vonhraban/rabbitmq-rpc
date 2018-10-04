<?php
require_once __DIR__ . '/vendor/autoload.php';
$store = new \Datix\Server\User\CSVUserStore();
print_r($store->get(9999));