<?php

// Sanitize GET Request
function a_get($str){
    $res = filter_input(INPUT_GET, $str, FILTER_SANITIZE_STRING);
    return $res;
}

// Sanitize POST Request
function a_post($str){
    $res = filter_input(INPUT_POST, $str, FILTER_SANITIZE_STRING);
    return $res;
}

// Sanitize SERVER Request
function a_server($str){
    $res = filter_input(INPUT_SERVER, $str, FILTER_SANITIZE_STRING);
    return $res;
}

