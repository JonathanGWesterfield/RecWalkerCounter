<?php
/**
 * Created by PhpStorm.
 * Date: 2/8/18
 * Time: 4:36 PM
 */

include_once "DBAPI.php";

interface DBInterface
{
    /**
     * DBAPI constructor.
     * @param $db
     *
     * Mostly sets up the dates in this object. Also sets the timezone to our timezone.
     */
    public function __construct($db);

    // class Destructor
    public function __destruct();

    // prints out the entire database on the page in a table
    public function printEntireDB();

    /**
     * @return int
     *
     * Returns the total number of walkers in the table
     */
    public function getTotalNumWalkers();

    /**
     * @return int
     *
     * Returns the number of walkers that walked through from the start to the end of the day
     */
    public function getNumWalkersToday();

    /**
     * @return int
     *
     * Returns the number of walkers from the start to the end of the week
     */
    public function getNumWalkersThisWeek();

    //public function getHotTimes(); // gets the hours of day with peak traffic
    //public function getHotDays(); // gets the days with peak traffic for a week

    /**
     * @return array
     *
     * Returns an array containing total number of walkers for each month for the current year
     * 12 Element Array
     */
    public function getCurrentYearTraffic();

    /**
     * @param $year
     * @return array
     *
     * Gives the traffic for each month in an array for the specified year passed in
     */
    public function getTrafficByYear($year);

    /**
     * @return array
     *
     * Returns an array containing total number of walkers for each day for the current month
     */
    public function getCurrentMonthTraffic();

    /**
     * @param $year
     * @param $month
     * @return array
     *
     * Gets the traffic for each day during the specified month of the specified year
     *
     * Usage: getTrafficByMonth(2018, 2); // for February 2018
     */
    public function getTrafficByMonth($year, $month);

    /**
     * @return array
     *
     * Returns a length 24 array containing total number of walkers for each hour
     * for the current day
     */
    public function getCurrentDayTraffic();

    /**
     * @param $year
     * @param $month
     * @param $day
     * @return array
     *
     * Returns an array (24) containing total number of walkers for each hour
     *
     * Usage: <var = getTrafficByDay(2018, 2, 15);> for February 15, 2018
     */
    public function getTrafficByDay($year, $month, $day);

    /**
     * @param $year1
     * @param $month1
     * @param $day1
     * @param $year2
     * @param $month2
     * @param $day2
     * @return int
     *
     * Takes in a date range (start and end date) and counts the number of walkers in the given range
     */
    public function getTrafficTimeRange($year1, $month1, $day1, $year2, $month2, $day2);
}

?>