<?php

$url = getenv('JAWSDB_URL');

if($url != null) {
    $dbparts = parse_url($url);

    $hostname = $dbparts['host'];
    $username = $dbparts['user'];
    $password = $dbparts['pass'];
    $database = ltrim($dbparts['path'],'/');
    
    $connection = mysqli_connect($hostname, $username, $password, $database);
    
} else {
    $db['db_host'] = "localhost";
    $db['db_user'] = "root";
    $db['db_pass'] = "";
    $db['db_name'] = "cms";
    
    foreach($db as $key => $value) {
        define(strtoupper($key), $value);
    }
    
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
}
