<?php
/**
 * Created by PhpStorm.
 * User: Cris
 * Date: 10/1/2018
 * Time: 4:10 PM
 */

 class info
{
    public $width=7;
    public $height=6;
    public $strategies =["Smart","Random"];

    function _construct($width, $height, $strategies)
    {

        $this->width = $width;
        $this->height = $height;
        $this->strategies = $strategies;
    }
}

$json = new info(7,6,["Random","Smart"]);
echo json_encode($json);


