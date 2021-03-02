<?php
require_once("db.php");

$countUsers= 10000;
$countProducts = 1000000;

$db = new DB();

$db->generateUser();

for ($i = 1; $i < $countProducts; $i++) {
//    $db->generateProduct();

}

for ($i = 1; $i < $countUsers; $i++) {
//    $db->generateUser();

}

//var_dump($db->showYearAgoRegUsers());
//var_dump($db->showOldUsers());


