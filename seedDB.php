<?php
require_once("db.php");

$countUsers= 100;
$countProducts = 1;

$db = new DB();

$db->generateUser();

for ($i = 1; $i < $countProducts; $i++) {
    $db->generateProduct();
}

for ($i = 1; $i < $countUsers; $i++) {
    $db->generateUser();
}

//var_dump($db->showYearAgoRegUsers());
//var_dump($db->showOldUsers());
//var_dump($db->showBirthDayUsers());


