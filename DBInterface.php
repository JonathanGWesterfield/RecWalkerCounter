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
    public function __construct($db);
    public function __destruct();
    public function printEntireDB();
    public function getTotalNumWalkers();
    public function getNumWalkersToday();
    public function getNumWalkersThisWeek();
    //public function getHotTimes(); // gets the hours of day with peak traffic
    //public function getHotDays(); // gets the days with peak traffic for a week
    public function getCurrentYearTraffic(); // Returns an array containing total number of walkers for each month for the current year
    public function getTrafficByYear($year); // Returns an array containing total number of walkers for each month.
    public function getCurrentMonthTraffic(); // Returns an array containing total number of walkers for each day for the current month
    public function getTrafficByMonth($year, $month); // Returns an array containing total number of walkers for each day.
    public function getCurrentDayTraffic(); // Returns an array containing total number of walkers for each hour for the current day
    public function getTrafficByDay($year, $month, $day); // Returns an array containing total number of walkers for each hour
}

?>