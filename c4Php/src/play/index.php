<?php
/**
 * Created by PhpStorm.
 * User: Cris
 * Date: 10/8/2018
 * Time: 4:53 PM
 */
$GLOBALS['player'] = []; //Array of moves made by the player
$GLOBALS['AI']= []; //Array of moves made by the AI
$GLOBALS['counter'] = [0,0,0,0,0,0,0]; //Array that keeps track of the y-coordinates of all columns.
$GLOBALS['userWinningPlay']=[]; //Array that contains the user winning play
$GLOBALS['AIWinningPlay']=[]; //Array that contains the AI winning play
$GLOBALS['gameOver'] = false; //Is the game over

$response = "true";
$pid = $_GET["pid"];
while (!$GLOBALS['gameOver']) { //keeps iterating until the game ends
    if ($GLOBALS['strategy'] == "random") {

        $slot = $_GET["move"]; //Player slot
        $ack_move = new ack_move($slot); //ack_move is the method for the player. It makes the player's moves.
        $move = new move(rand(0, 6)); //move is the method for the AI. It make's the AI's moves.

        /* at this point, both players have made their moves*/

        /* Asking "has anyone won yet?"*/
        if($GLOBALS['userWinningPlay']==null) //If the user did not make a winning play
            $winningResult = "";              //return an empty string
        else
            $winningResult = implode(",", $GLOBALS['userWinningPlay']); //If the user made a winning play return a comma-separated list of the play.
        if($GLOBALS['AIWinningResult'] == null)                     //If the AI did not make a winning play
            $AIWinningResult = "";                                  //return an empty string
        else
            $AIWinningResult = implode(",", $GLOBALS['AIWinningPlay']); //If the AI made a winning play return a comma-separated list of the play.

        /*Creates a string containing everything that happened during this turn*/
        $String_for_Server = "{" . "\"response\":" . $response . "," . "\"ack_move\":" . "{" . "\"slot\":" . $ack_move->slot . "," . "\"isWin\":" . $ack_move->isWin($ack_move->slot) . "\"isDraw\":" . $ack_move->isDraw() . "," . "\"row\"" . "[". $winningResult . "]" . "}," . "\"move\":" . "{" . "\"slot\":" . $move->$slot . "," . "\"isWin\":" . $move->isWin($move->slot) . "," . "\"isDraw\":" . $move->isDraw() . "," . "\"row\":" . "[" . $AIWinningResult . "]" . "}}";

        echo json_encode($String_for_Server); //sends information about this turn to the server

    }
    /*this else will only execute if the selected strategy is Smart. Really, it does the exact same thing as if
    the user had selected the Random Strategy because I never got to code the Smart AI*/
    else {
        $slot = $_GET["move"];
        $ack_move = new ack_move($slot); //user makes its move
        $move = new move(rand(0, 6)); //Ai makes its move (its not smart tho)
        if($GLOBALS['userWinningPlay']==null)
            $winningResult = "";
        else
            $winningResult = implode(",", $GLOBALS['userWinningPlay']);
        if($GLOBALS['AIWinningResult'] == null)
            $AIWinningResult = "";
        $String_for_Server = "{" . "\"response\":" . $response . "," . "\"ack_move\":" . "{" . "\"slot\":" . $ack_move->slot . "," . "\"isWin\":" . $ack_move->isWin($ack_move->slot) . "\"isDraw\":" . $ack_move->isDraw() . "," . "\"row\"" . "[" . $winningResult . "]" . "}," . "\"move\":" . "{" . "\"slot\":" . $move->$slot . "," . "\"isWin\":" . $move->isWin($move->slot) . "," . "\"isDraw\":" . $move->isDraw() . "," . "\"row\":" . "[" . $AIWinningResult . "]". "}}";

        echo json_encode($String_for_Server);

    }



}

###################################### ACK_Move########################################################################
class ack_move{ //User-only method, executes every user's turn
    public $slot; //slot chosen by the player

    /**
     * ack_move constructor.
     * @param $slot
     */
    public function __construct($slot)
    {
        $this->slot =$slot;
        //Adds a new coordinate to the end of the array of moves made by the player
        array_push($GLOBALS['player'], $coor = new Coordinate($slot,$GLOBALS['counter'][$slot]));
    }




    function isWin($slot){ //checks win conditions
        if($this->checkHorizontal($slot) == "true") { //checks horizontal line
            $GLOBALS['gameOver'] = true;
            return "true";
        }
        if($this->checkVertical($slot) == "true") { //checks vertical line
            $GLOBALS['gameOver'] = true;
            return "true";
        }
        if($this->checkDiagonalXLeftYUp_XRightYDown($slot) == "true") { //checks left diagonal line (\)
            $GLOBALS['gameOver'] = true;
            return "true";
        }
        if($this->checkDiagonalXRightYUp_XLeftYDown($slot) == "true") {//checks right diagonal line (/)
            $GLOBALS['gameOver'] = true;
            return "true";
        }
        else
            return "false";
    }
    function isDraw(){ //checks if the board if full

        $arrayLength = count($GLOBALS['counter']); //Gets the array length of the counter array
        for ($i=0; $i<$arrayLength; $i++) {
            if ($GLOBALS['counter'][$i] != 5) //Checks if there's a column that's not full, 5 is the maximum y-coordinate of the grid.
                return "false";               //If there's a column that's not full, then the game is not a draw.
            else {
                $GLOBALS['gameOver'] = true;  //If all the columns are full then the game is over.
                return "true";
            }

        }
    }

    function checkHorizontal($slot){
        $didIwin="false"; //Tells you if you won
        $arrayLength = count($GLOBALS['player']); //Sets array length to the size of the player array of coordinates.
        $y = $GLOBALS['counter'][$slot]; //Sets the value of the y-coordinate to an index of the counter array
        $xRight = $slot+1; //slot is the x-coordinate, so x+1 = xRight
        $xLeft = $slot-1;  //x-1 = xLeft
        $inaRow =0; //Keeps track of the amount of tokens in a row.

        for ($i = 0; $i<$arrayLength; $i++){ //Checks if the xRight coordinate is in the player array
            $coor = new Coordinate($xRight,$y); //coordinate array (x,y)

            if(!in_array($coor, $GLOBALS['player'])) //checks if x+1,y exists in the array, if not, stop the loop
                break;
            else
                array_push($GLOBALS['userWinningPlay'], $xRight); //If the coordinate is in the array, add the x+1
            array_push($GLOBALS['userWinningPlay'], $y);          //coordinate to the userWinningPlay array
            $inaRow++;
            if($inaRow == 3) {                  //If there's 4 in a row then
                $didIwin = "true";              //the player won
                return $didIwin;
            }

            $xRight++; //increases xRight to check one more to the right
        }
        for ($i=0; $i<$arrayLength; $i++){ //Checks if the xLeft coordinate is in the array

            $coor = new Coordinate($xLeft,$y); //coordinate array (x,y)
            if(!in_array($coor, $GLOBALS['player'])) //checks if x-1,y exists in the array
                break;
            else
                array_push($GLOBALS['userWinningPlay'],$xLeft);
            array_push($GLOBALS['userWinningPlay'], $y);
            $inaRow++;
            if($inaRow==3) {
                $didIwin="true";
                return $didIwin;
            }
            $xLeft--;
        }

    }

    function checkVertical($slot){ //Does the same as checkHorizontal but with y values instead of x values
        $inaRow =0;
        $arrayLength = count($GLOBALS['player']);
        $yUp = $GLOBALS['counter'][$slot+1];
        $yDown = $GLOBALS['counter'][$slot-1];
        for ($i = 0; $i<$arrayLength; $i++){

            $coor = new Coordinate($slot,$yUp); //coordinate array (x,y)
            if(!in_array($coor, $GLOBALS['player'])) //checks if x,y+1 exists in the array
                break;
            else
                array_push($GLOBALS['userWinningPlay'], $slot);
            array_push($GLOBALS['userWinningPlay'], $yUp);
            $inaRow++;
            if($inaRow==3){
                $didIwin= "true";
                return $didIwin;
            }
            $yUp++;
        }
        for ($i = 0; $i<$arrayLength; $i++){

            $coor = new Coordinate($slot,$yDown); //coordinate array (x,y)
            if(!in_array($coor, $GLOBALS['player'])) //checks if x,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['userWinningPlay'], $slot);
            array_push($GLOBALS['userWinningPlay'], $yDown);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yDown--;
        }
    }

    function checkDiagonalXRightYUp_XLeftYDown($slot){
        $arrayLength = count($GLOBALS['player']);
        $inaRow = 0;
        $yUp = $GLOBALS['counter'][$slot+1];
        $xRight = $slot+1;
        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xRight, $yUp);
            if(!in_array($coor, $GLOBALS['player'])) //checks if x+1,y+1 exists in the array
                break;
            else
                array_push($GLOBALS['userWinningPlay'],$xRight);
            array_push($GLOBALS['userWinningPlay'], $yUp);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yUp++;
            $xRight++;
        }

        $yDown = $GLOBALS['counter'][$slot-1];
        $xLeft = $slot-1;

        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xLeft, $yDown);
            if(!in_array($coor, $GLOBALS['player'])) //checks if x-1,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['userWinningPlay'],$xLeft);
            array_push($GLOBALS['userWinningPlay'],$yDown);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yDown--;
            $xLeft--;
        }
    }

    function checkDiagonalXLeftYUp_XRightYDown($slot){
        $arrayLength = count($GLOBALS['player']);
        $inaRow = 0;
        $yUp = $GLOBALS['counter'][$slot+1];
        $xLeft = $slot-1;
        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xLeft, $yUp);
            if(!in_array($coor, $GLOBALS['player'])) //checks if x-1,y+1 exists in the array
                break;
            else
                array_push($GLOBALS['userWinningPlay'],$xLeft);
            array_push($GLOBALS['userWinningPlay'],$yUp);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yUp++;
            $xLeft--;
        }

        $yDown = $GLOBALS['counter'][$slot-1];
        $xRight = $slot+1;

        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xRight, $yDown);
            if(!in_array($coor, $GLOBALS['player'])) //checks if x+1,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['userWinningPlay'],$xRight);
            array_push($GLOBALS['userWinningPlay'],$yDown);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yDown--;
            $xRight++;
        }
    }

}

##########################################################MOVE####################################################################################

class move{
    public $slot;

    /**
     * ack_move constructor.
     * @param $slot
     * @param array $player
     * @param array $counter
     */
    public function __construct($slot)
    {
        $this->slot =$slot;

        array_push($GLOBALS['AI'], $coor = new Coordinate($slot,$GLOBALS['counter'][$slot]));
    }




    function isWin($slot)
    {
        if ($this->checkHorizontal($slot) == "true") {
            $GLOBALS['gameOver'] = true;
            return "true";
        }
        if ($this->checkVertical($slot) == "true"){
            $GLOBALS['gameOver'] = true;
            return "true";
        }
        if($this->checkDiagonalXLeftYUp_XRightYDown($slot) == "true") {
            $GLOBALS['gameOver'] = true;

            return "true";
        }
        if($this->checkDiagonalXRightYUp_XLeftYDown($slot) == "true") {
            $GLOBALS['gameOver'] = true;

            return "true";
        }
        else
            return "false";
    }
    function isDraw(){

        $arrayLength = count($GLOBALS['counter']);
        for ($i=0; $i<$arrayLength; $i++) {
            if ($GLOBALS['counter'][$i] != 5) //6 is the size of the grid's size
                return "false";
            else {
                $GLOBALS['gameOver'] = true;
                return "true";
            }

        }
    }

    function checkHorizontal($slot){
        $didIwin="false";
        $arrayLength = count($GLOBALS['AI']);
        $y = $GLOBALS['counter'][$slot];
        $xRight = $slot+1;
        $xLeft = $slot-1;
        $inaRow =0;

        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xRight,$y); //coordinate array (x,y)

            if(!in_array($coor, $GLOBALS['AI'])) //checks if x+1,y exists in the array
                break;
            else
                array_push($GLOBALS['AIWinningPlay'], $xRight);
            array_push($GLOBALS['AIWinningPlay'], $y);
            $inaRow++;
            if($inaRow == 3) {
                $didIwin = "true";
                return $didIwin;
            }

            $xRight++;
        }
        for ($i=0; $i<$arrayLength; $i++){

            $coor = new Coordinate($xLeft,$y); //coordinate array (x,y)
            if(!in_array($coor, $GLOBALS['AI'])) //checks if x-1,y exists in the array
                break;
            else
                array_push($GLOBALS['AIWinningPlay'],$xLeft);
            array_push($GLOBALS['AIWinningPlay'], $y);
            $inaRow++;
            if($inaRow==3) {
                $didIwin="true";
                return $didIwin;
            }
            $xLeft--;
        }

    }

    function checkVertical($slot){
        $inaRow =0;
        $arrayLength = count($GLOBALS['AI']);
        $yUp = $GLOBALS['counter'][$slot+1];
        $yDown = $GLOBALS['counter'][$slot-1];
        $didIwin="false";
        for ($i = 0; $i<$arrayLength; $i++){

            $coor = new Coordinate($slot,$yUp); //coordinate array (x,y)
            if(!in_array($coor, $GLOBALS['AI'])) //checks if x,y+1 exists in the array
                break;
            else
                array_push($GLOBALS['AIWinningPlay'], $slot);
            array_push($GLOBALS['AIWinningPlay'], $yUp);
            $inaRow++;
            if($inaRow==3){
                $didIwin= "true";
                return $didIwin;
            }
            $yUp++;
        }
        for ($i = 0; $i<$arrayLength; $i++){

            $coor = new Coordinate($slot,$yDown); //coordinate array (x,y)
            if(!in_array($coor, $GLOBALS['AI'])) //checks if x,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['AIWinningPlay'], $slot);
            array_push($GLOBALS['AIWinningPlay'], $yDown);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yDown--;
        }
    }

    function checkDiagonalXRightYUp_XLeftYDown($slot){
        $arrayLength = count($GLOBALS['AI']);
        $inaRow = 0;
        $yUp = $GLOBALS['counter'][$slot+1];
        $xRight = $slot+1;
        $didIwin="false";
        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xRight, $yUp);
            if(!in_array($coor, $GLOBALS['AI'])) //checks if x,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['AIWinningPlay'],$xRight);
            array_push($GLOBALS['AIWinningPlay'], $yUp);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yUp++;
            $xRight++;
        }

        $yDown = $GLOBALS['counter'][$slot-1];
        $xLeft = $slot-1;

        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xLeft, $yDown);
            if(!in_array($coor, $GLOBALS['AI'])) //checks if x,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['AIWinningPlay'],$xLeft);
            array_push($GLOBALS['AIWinningPlay'],$yDown);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yDown--;
            $xLeft--;
        }
    }

    function checkDiagonalXLeftYUp_XRightYDown($slot){
        $arrayLength = count($GLOBALS['AI']);
        $inaRow = 0;
        $yUp = $GLOBALS['counter'][$slot+1];
        $xLeft = $slot-1;
        $didIwin = "false";
        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xLeft, $yUp);
            if(!in_array($coor, $GLOBALS['AI'])) //checks if x,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['AIWinningPlay'],$xLeft);
            array_push($GLOBALS['AIWinningPlay'],$yUp);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yUp++;
            $xLeft--;
        }

        $yDown = $GLOBALS['counter'][$slot-1];
        $xRight = $slot+1;

        for ($i = 0; $i<$arrayLength; $i++){
            $coor = new Coordinate($xRight, $yDown);
            if(!in_array($coor, $GLOBALS['AI'])) //checks if x,y-1 exists in the array
                break;
            else
                array_push($GLOBALS['AiWinningPlay'],$xRight);
            array_push($GLOBALS['AiWinningPlay'],$yDown);
            $inaRow++;
            if($inaRow==3) {
                $didIwin = "true";
                return $didIwin;
            }
            $yDown--;
            $xRight++;
        }
    }

}

###############################################################COORDINATE#######################################################################
class Coordinate
{
    public $x;
    public $y;

    function Coordinate($x, $y)
    {
        $this->$x = $x;
        $this->$y = $y;

    }
    function toString(){
        return $this->x . "," . $this->y;
    }
}

#################################################################################################################################################