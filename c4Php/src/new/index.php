<?php
/**
 * Created by PhpStorm.
 * User: Cris
 * Date: 10/1/2018
 * Time: 4:10 PM
 * {"response":true,"pid":"5bb3b7f26c443"}
 */


$response = "false";
$pid = uniqid();
$GLOBALS['strategy'] = $_GET["strategy"];

    if($GLOBALS['strategy'] == "Smart" || $GLOBALS['strategy'] == "Random") {
        $response = "true";
        echo "{". "\"response\":". $response . "," . "\"pid\":" . "\"$pid\"". "}";
    }

    else {
        $reason = "Unknown strategy";
        echo "'response': " . $response  . " 'reason': ". $reason;


    }
