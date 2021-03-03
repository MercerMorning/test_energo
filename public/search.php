<?php
    require_once("../db.php");
    if ($_GET) {
        $db = new DB();
        $results = $db->searchProducts($_GET);
    }
    include "index.php";
?>