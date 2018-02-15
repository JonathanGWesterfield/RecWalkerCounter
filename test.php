<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 5:05 PM
 */

error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

ini_set('error_log', './script_errors.log');  // change here
ini_set('log_errors', 'On');

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
$test->getNumWalkersToday();
$test->getNumWalkersThisWeek();
$test->getCurrentYearTraffic();

?>