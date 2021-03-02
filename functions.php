<?php
function dd($arr) {
    foreach ($arr as $r) {
        var_dump($r);
        echo '<br>';
    }
}

function one($arrays) {
    return $result = array_intersect($arrays[0], $arrays[1], $arrays[2]);
//    foreach ($arrays as $array) {
//        var_dump($array);
//        echo '<br>';
//    }
}