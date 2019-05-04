<?php
/**
 * Created by PhpStorm.
 * User: Cris
 * Date: 10/20/2018
 * Time: 7:26 PM
 */

$x = -20; //initial declaration

for($x = 0; $x < 3; $x++){
    echo '<p>'. " The value of x is: " . $x . '</p>';
}
echo '<p>'." The value of x outside the loop is: " . $x . '</p>';

do{
    echo '<p>' . " The value of y is: " . $y.  '</p>' ;
    $y++;
}while ($y <3);
echo '<p>'. " The value of y outside the loop is: " . $y.  '</p>' ;