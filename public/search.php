<?php
    require_once("../db.php");
    if ($_GET) {
//        var_dump($_GET);
        $db = new DB();
        $results = $db->searchProducts($_GET);
        var_dump($results);
    }
//    include "index.php";
?>