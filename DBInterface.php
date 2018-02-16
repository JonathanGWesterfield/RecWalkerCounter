<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 4:36 PM
 */

include_once "DBAPI.php";

interface DBInterface
{
    // class constructor
    public function __construct($db);

    // class Destructor
    public function __destruct();
    // prints out the entire database on the page in a table
    public function printEntireDB();

    // returns the total number of walkers in the database
    public function getTotalNumWalkers();

    // gets the number of walkers that walked through from the start to the end of the day
    public function getNumWalkersToday();

    // gets the number of walkers from the start to the end of the week
    public function getNumWalkersThisWeek();

    //public function getHotTimes(); // gets the hours of day with peak traffic
    //public function getHotDays(); // gets the days with peak traffic for a week

    // Returns an array containing total number of walkers for each month for the current year
    public function getCurrentYearTraffic();

    // Returns an array containing total number of walkers for each month.
    public function getTrafficByYear($year);

    // Returns an array containing total number of walkers for each day for the current month
    public function getCurrentMonthTraffic();

    // Returns an array containing total number of walkers for each day.
    public function getTrafficByMonth($year, $month);

    // Returns an array containing total number of walkers for each hour for the current day
    public function getCurrentDayTraffic();

    // Returns an array (24) containing total number of walkers for each hour
    public function getTrafficByDay($year, $month, $day);
}

?>