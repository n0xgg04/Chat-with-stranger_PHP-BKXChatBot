<?php
error_reporting(E_ALL);

$client = new MongoDB\Client(
    'mongodb+srv://n0xgg04:n0xgg04Anh@cluster0.ipsz2cm.mongodb.net/?retryWrites=true&w=majority', [], ['serverApi' => $serverApi]);
$db = $client->test;