<?php

include("config.php");

if ($_POST['password'] !== $_CONFIG['password']) {
    http_response_code(403);
    die("Invalid password!");
}

$fpath = "addresses.json";
if (file_exists($fpath)) {
    $adds = json_decode(file_get_contents($fpath));
} else {
    $adds = [];
}

$fields = ['GEOC_LAT', 'GEOC_LNG', 'title', 'content', 'location'];
$record = [];
foreach ($fields as $field) {
    $record[$field] = $_POST[$field];
}
$adds[] = $record;

file_put_contents($fpath, json_encode($adds));

die("ok!");