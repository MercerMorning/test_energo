<?php
function dd($arr) {
    foreach ($arr as $r) {
        var_dump($r);
        echo '<br>';
    }
}

function one($arrays){
    $result = array_intersect($arrays[0], $arrays[1], $arrays[2]);;
//    $result = $arrays[1];
    return $result;
}