<?php


/**
 * 随机产生字符串
 * @param int $len
 * @return string
 */
function randStr($len=6) {
    $chars='ABDEFGHJKLMNPQRSTVWXYabdefghijkmnpqrstvwxy23456789'; // characters to build the password from
    mt_srand((double)microtime()*1000000*getmypid()); // seed the random number generater (must be done)
    $password='';
    while(strlen($password)<$len)
        $password.=substr($chars,(mt_rand()%strlen($chars)),1);
    return $password;
}