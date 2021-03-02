<?php
require_once("db.php");

$countProducts = 1000000;

$db = new DB();

for ($i = 1; $i <= $countProducts; $i++) {
    $db->generateProduct();
}



