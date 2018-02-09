<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 5:05 PM
 */

include_once "CommonInterface.php";
include_once "DBInterface.php";


echo("Attempting to start<br>");

$thisCommon = new Common(true);

echo("Started the Common<br>");
echo("Starting the API<br>");

$test = new DBAPI($thisCommon);

echo("Created the DBAPI object<br>");

$test->printEntireDB();
$test->getTotalNumWalkers();

?>