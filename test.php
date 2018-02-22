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
$monthArray = $test->getCurrentYearTraffic();

echo("<br> FROM THE TEST FILE <br>");
foreach ($monthArray as $element)
{
    echo($element . " ");
}
echo("<br><br>");

$dayArray = $test->getCurrentMonthTraffic();
echo("<br> FROM THE TEST FILE <br>");
foreach ($dayArray as $element)
{
    echo($element . " ");
}
echo("<br><br>");

$hourArray = $test->getCurrentDayTraffic();

echo("FROM THE TEST FILE: <br>");
foreach ($hourArray as $element)
{
    echo($element . " "); // print out the array (will be starting from December to January)
}
echo("<br><br>");

$secondMonthArray = $test->getTrafficByYear(2018);
echo("<br> FROM THE TEST FILE <br>");
foreach ($secondMonthArray as $element)
{
    echo($element . " ");
}
echo("<br><br>");

$secondDayArray = $test->getTrafficByMonth(2020, 2);
echo("<br> FROM THE TEST FILE <br>");
foreach ($secondDayArray as $element)
{
    echo($element . " ");
}
echo("<br><br>");

$secondHourArray = $test->getTrafficByDay(2018, 2, 15);

echo("FROM THE TEST FILE: <br>");
foreach ($secondHourArray as $element)
{
    echo($element . " "); // print out the array (will be starting from December to January)
}
echo("<br><br>");

/**
 * Unit tests start here
 */

if($test->getTotalNumWalkers() != 17)
{
    echo("getTotalNumWalkers() failed! Didn't equal 17 (the number of rows in the DB<br>");
}

//TODO: write test cases to make sure the objects data (dates) are correct for the class
if($test->getNumWalkersToday() != 0)
{
    echo("getNumWalkersToday() failed! There are supposed to be 0 walkers today! <br>");
}

if(count($monthArray != 12))
{
    echo("Error in getCurrentYearTraffic()!!<br>");
    echo("Did not return 12 months in the year!<br>");
}

if(array_sum($monthArray) != 17)
{
    echo("Error in getCurrentYearTraffic()!!! Got the wrong number of walkers back!<br>");
}

/* TODO: Make more flexible testing to make sure that the functions are returning the correct things
    for the correct day, week, month */

?>