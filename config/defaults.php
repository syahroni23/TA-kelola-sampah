<?php
if (!file_exists(__DIR__ . '/../routes/session')) {
	mkdir(__DIR__ . '/../routes/session', 0777, true);
}

session_start();

date_default_timezone_set('Asia/Jakarta');

$directoryURI = $_SERVER['REQUEST_URI'];
$parseURI = parse_url($directoryURI, PHP_URL_PATH);
$componentsURI = explode('/', $parseURI);
$pathURI = $componentsURI[3];
?>