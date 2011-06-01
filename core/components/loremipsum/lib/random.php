<?php

function seed_rand_ndn($seed) {
    srand($seed);
    mt_srand($seed);
}

function rand_ndn($n) {
    $sum = 0;
    for($i=0; $i<$n; $i++) {
        $sum += rand(0, getrandmax())/getrandmax();
    }
    return $sum/$n;
}

function mt_rand_ndn($n) {
    $sum = 0;
    for($i=0; $i<$n; $i++) {
        $sum += mt_rand(0, mt_getrandmax())/mt_getrandmax();
    }
    return $sum/$n;
}

/* fastest, but not directly seedable */
function lcg_rand_ndn($n) {
    $sum = 0;
    for($i=0; $i<$n; $i++) {
        $sum += lcg_value();
    }
    return $sum/$n;
}

function lcg_probability($chance) {
    return lcg_value() <= $chance;
}

/*
seed_rand_ndn(1);

$stack = array();
for($i=0; $i<2000; $i++) {
    #$n = round(rand_ndn(9)*10);
    #$n = round(mt_rand_ndn(9)*10);
    $n = round(lcg_rand_ndn(3)*10);
    @$stack[$n] += 1;
}

ksort($stack);
var_dump($stack);
var_dump(xdebug_time_index());
*/
