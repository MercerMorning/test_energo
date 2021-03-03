<?php
require_once('../db.php');
if (strpos($_SERVER['REQUEST_URI'], '/search') === 0) {
    include 'searchForm.php';
}
if (strpos($_SERVER['REQUEST_URI'], '/api/search') === 0) {
    $db = new DB();
    echo $db->apiSearchProducts($_GET);
    header("HTTP/1.0 200 OK");
}
