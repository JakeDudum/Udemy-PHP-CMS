<?php

$JAWSdb_url = parse_url(getenv("JAWSDB_DATABASE_URL"));
$JAWSdb_server = $JAWSdb_url["host"];
$JAWSdb_username = $JAWSdb_url["user"];
$JAWSdb_password = $JAWSdb_url["pass"];
$JAWSdb_db = substr($JAWSdb_url["path"],1);
$active_group = 'default';
$query_builder = TRUE;

// Connect to DB
$conn = mysqli_connect($JAWSdb_server, $JAWSdb_username, $JAWSdb_password, $JAWSdb_db);

?>