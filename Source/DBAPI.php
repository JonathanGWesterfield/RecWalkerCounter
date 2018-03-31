<?php
/**
 * Created by PhpStorm.
 * Date: 2/8/18
 * Time: 4:35 PM
 *
 * Includes the various functions that can be used in order to extract useful information from the database.
 * Implements DBInterface.php
 */

include_once "CommonInterface.php";
include_once "DBInterface.php";

class DBAPI implements DBInterface
{
    private $COMMON;
    private $currentDate;
    private $currentDateTime;
    private $currentWeekDay;
    private $tomorrowDate;
    private $currentYear;
    private $nextYear;
    private $currentMonth;

    /**
     * DBAPI constructor.
     * @param $db
     *
     * Mostly sets up the dates in this object. Also sets the timezone to our timezone.
     */
    public function __construct($db)
    {
        // echo("<br>Constructor has been called<br>");

        $this->COMMON = $db;

        date_default_timezone_set('America/Chicago');

        // Check to make sure the timezone the server is in is set correctly (to College Station)
        if (date_default_timezone_get() != 'America/Chicago')
        {
            // echo("ERROR! TIMEZONE SET INCORRECTLY!!! NOT SET TO COLLEGE STATION'S TIME (CHICAGO)!!<br>");
            // echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br/>';
            exit(1);
        }
        // echo("Default Timezone set to: " . date_default_timezone_get() . "<br>");

        $this->currentDate = date("Y-m-d");
        $this->currentDateTime = date("Y-m-d H:i:s");
        $this->currentWeekDay = date('w');
        $this->tomorrowDate = date('Y-m-d', strtotime('+1 day'));
        $this->currentYear = date("Y");
        $this->currentMonth = date("m");
        $this->nextYear = date('Y', strtotime('+1 year'));
        $this->nextYear = $this->nextYear . "-01-01";

        // echo("Current Date: " . $this->currentDate . "<br>");
        // echo("Current DateTime: " . $this->currentDateTime . "<br>");
        // echo("Current weekday numerically: " . $this->currentWeekDay . "<br>");
        // echo("Tomorrow's Date: " . $this->tomorrowDate . "<br>");
        // echo("The current year is: " . $this->currentYear . "<br>");
        // echo("The current month is: " . $this->currentMonth . "<br>");
        // echo("Next year is: " . $this->nextYear . "<br>");
        // echo("<br><br>");
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        // echo("<br>Destructor has been called <br>");
        // echo("DESTROYING EVERTYING<br>");
    }

    /**
     * Prints out a table of the entire database
     */
    public function printEntireDB()
    {
        $sql = "SELECT * FROM WalkerData WHERE InOrOut=1 ORDER BY WalkerNumber";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        echo("<table border='1px'>");

        while($row = $rs->fetch(PDO::FETCH_ASSOC))
        {
            echo("<tr>");
            foreach ($row as $element)
            {
                echo("<td>".$element."</td>");
            }
            echo("</tr>");
        }
        echo("</table");
    }

    /**
     * @return int
     *
     * Gets the total number of walkers in the table
     */
    public function getTotalNumWalkers()
    {
        // echo("<br><br>getTotalNumWalkers<br>");
        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE InOrOut=1";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        // echo("Total Num Walkers is: " . $row['COUNT(WalkerNumber)'] . "<br>");

        return (int)$row['COUNT(WalkerNumber)'];
    }

    /**
     * @return int
     *
     * Gets the number of walkers from Today
     */
    public function getNumWalkersToday()
    {
        // echo("<br><br>getNumWalkersToday");
        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" . $this->currentDate . "%\" AND \""
            . $this->tomorrowDate . "%\" AND InOrOut=1";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        // echo("Total Num Walkers is: " . $row['COUNT(WalkerNumber)'] . "<br>");

        return (int)$row['COUNT(WalkerNumber)'];
    }

    /**
     * @return int
     *
     * Gets the number of walkers in the last week starting from today
     */
    public function getNumWalkersThisWeek()
    {
        // echo("<br><br>getNumWalkersThisWeek<br>");
        $lastWeek = date('Y-m-d', strtotime('-1 week'));

        // echo("Last weeks date: " . $lastWeek . "<br>");

        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" . $lastWeek . "%\" AND \""
            . $this->tomorrowDate . "%\" AND InOrOut=1";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        // echo("Total Num Walkers is: " . $row['COUNT(WalkerNumber)'] . "<br>");

        return (int)$row['COUNT(WalkerNumber)'];
    }

    /**
     * @return array
     *
     * Returns an array of the traffic numbers for each month in a 12 element array
     */
    public function getCurrentYearTraffic()
    {
        $prevMonth = new DateTime($this->nextYear);

        $monthArray = []; // array for the numbers for the months

        for($i = 0; $i < 12; $i++) // get numbers for each month
        {
            $lookMonth = clone $prevMonth;
            $prevMonth->modify('-1 month'); // decrement month
            // echo $prevMonth->format('Y-m-d');

            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" . $prevMonth->format('Y-m-d') . "%\" AND \"" .
                $lookMonth->format('Y-m-d') . "%\" AND InOrOut=1";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            array_push($monthArray, $row['COUNT(WalkerNumber)']); // add the result to the month array
        }

        /* echo("The numbers for this year starting from the end of the year: ");
        foreach (array_reverse($monthArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }
        echo("<br><br>"); */
        return array_reverse($monthArray); // reverse the array to start in January instead of December
    }

    /**
     * @param $year
     * @return array
     *
     * Gives the traffic for each month in an array for the specified year passed in
     */
    public function getTrafficByYear($year)
    {
        $thisYear = new DateTime((string)$year . "-01-01");
        $nextYear = clone $thisYear;
        $nextYear->modify('+1 year');

        $prevMonth = clone $nextYear;

        $monthArray = []; // array for the numbers for the months

        for($i = 0; $i < 12; $i++) // get numbers for each month
        {
            $lookMonth = clone $prevMonth;
            $prevMonth->modify('-1 month'); // decrement month
            // echo $prevMonth->format('Y-m-d');

            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" .
                $prevMonth->format('Y-m-d') . "%\" AND \"" .
                $lookMonth->format('Y-m-d') . "%\" AND InOrOut=1";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            array_push($monthArray, $row['COUNT(WalkerNumber)']); // add the result to the month array
        }

        // echo("The numbers for this year starting from the end of the year: ");
        /*foreach (array_reverse($monthArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>");*/

        return array_reverse($monthArray); // reverse the array to start in January instead of December
    }

    /**
     * @return array
     *
     * Gets the traffic numbers for each day during the current month.
     */
    public function getCurrentMonthTraffic()
    {
        // get the very first day of the month
        $thisMonth = new DateTime($this->currentYear . "-" . $this->currentMonth . "-01");
        // echo("The beginning of this month: " . $thisMonth->format('Y-m-d') . "<br>");

        // copy to make the next Month's object
        $nextMonth = clone $thisMonth;

        // get the next month's date by incrementing a month
        $nextMonth->modify('+1 month');
        // echo("Next Month is: " . $nextMonth->format('Y-m-d') . "<br>");

        // calculate the # of days between the start and end of the month - # of days in the month
        $diff = $nextMonth->diff($thisMonth)->format("%a");

        // echo("Difference between the 2 months in days: " . $diff . "<br>");

        // create 2 days to look at the numbers between each day
        $lookDay = clone $nextMonth;
        $dayBefore = clone $nextMonth;
        $dayBefore->modify('-1 day');

        // echo("Look day: " . $lookDay->format('Y-m-d') . "<br>");
        // echo("Day Before: " . $dayBefore->format('Y-m-d') . "<br>");

        $dayArray = [];

        for($i = 0; $i < $diff; $i++)
        {
            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" .
                $dayBefore->format('Y-m-d') . "%\" AND \"" . $lookDay->format('Y-m-d') .
                "%\" AND InOrOut=1";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            // add the result to the day array
            array_push($dayArray, $row['COUNT(WalkerNumber)']);

            // decrement the target days for the next iteration
            $lookDay->modify("-1 day");
            $dayBefore->modify("-1 day");
        }

        /* echo("Numbers for the days of this current month: <br>");
        foreach (array_reverse($dayArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }
        echo("<br><br>");*/

        // return the days reversed since the original array starts from the end of the month
        return array_reverse($dayArray);
    }

    /**
     * @param $year
     * @param $month
     * @return array
     *
     * Gets the traffic for each day during the specified month of the specified year
     *
     * Usage: getTrafficByMonth(2018, 2); // for February 2018
     */
    public function getTrafficByMonth($year, $month)
    {
        // get the very first day of the month
        $thisMonth = new DateTime((string)$year . "-" . (string)$month . "-01");
        // echo("The beginning of this month: " . $thisMonth->format('Y-m-d') . "<br>");

        // copy to make the next Month's object
        $nextMonth = clone $thisMonth;

        // get the next month's date by incrementing a month
        $nextMonth->modify('+1 month');
        // echo("Next Month is: " . $nextMonth->format('Y-m-d') . "<br>");

        // calculate the # of days between the start and end of the month - # of days in the month
        $diff = $nextMonth->diff($thisMonth)->format("%a");

        // echo("Difference between the 2 months in days: " . $diff . "<br>");

        // create 2 days to look at the numbers between each day
        $lookDay = clone $nextMonth;
        $dayBefore = clone $nextMonth;
        $dayBefore->modify('-1 day');

        // echo("Look day: " . $lookDay->format('Y-m-d') . "<br>");
        // echo("Day Before: " . $dayBefore->format('Y-m-d') . "<br>");

        $dayArray = [];

        for($i = 0; $i < $diff; $i++)
        {
            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" .
                $dayBefore->format('Y-m-d') . "%\" AND \"" . $lookDay->format('Y-m-d') .
                "%\" AND InOrOut=1";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            // add the result to the day array
            array_push($dayArray, $row['COUNT(WalkerNumber)']);

            // decrement the target days for the next iteration
            $lookDay->modify("-1 day");
            $dayBefore->modify("-1 day");
        }

        // echo("Numbers for the days of this current month: <br>");
        /*foreach (array_reverse($dayArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>"); */

        // return the days reversed since the original array starts from the end of the month
        return array_reverse($dayArray);
    }

    /**
     * @return array
     *
     * Gets the number of people for every hour and returns a length 24 array
     */
    public function getCurrentDayTraffic()
    {
        // get the time of the absolute end of the day and the hour before that
        $endOfToday = new DateTime($this->tomorrowDate . " 00:00:00");
        $prevHour = clone $endOfToday;
        $prevHour->modify('-1 hour');

        // echo("End of Today: " . $endOfToday->format('Y-m-d H:i:s') . "<br>");
        // echo("End of Today - 1 hour: " . $prevHour->format('Y-m-d H:i:s') . "<br>");

        $hourArray = [];

        for($i = 0; $i < 24; $i++)
        {
            /** output the times to see if I overshot how many times to iterate */
            // echo("End of Today: " . $endOfToday->format('Y-m-d H:i:s') . "<br>");
            // echo("End of Today - 1 hour: " . $prevHour->format('Y-m-d H:i:s') . "<br>");

            // get the number of walkers in between the 2 times
            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" .
                $prevHour->format('Y-m-d H:i:s') . "\" AND \"" . $endOfToday->format('Y-m-d H:i:s') .
                "\" AND InOrOut=1";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            // push the result onto the array
            array_push($hourArray, $row['COUNT(WalkerNumber)']);

            // decrement the hour for the next iteration
            $endOfToday->modify('-1 hour');
            $prevHour->modify('-1 hour');
        }

        // echo("Numbers by hour: ");
        /* foreach (array_reverse($hourArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>"); */

        // reverse the array since it starts from the end of the day
        return array_reverse($hourArray);
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @return array
     *
     * Gets the traffic for each hour and returns it in a 24 element array.
     *
     * Usage: <var = getTrafficByDay(2018, 2, 15);> for Febraury 15, 2018
     */
    public function getTrafficByDay($year, $month, $day)
    {
        // get the next day to get the correct date
        $day += 1;
        // get the time of the absolute end of the day and the hour before that
        $endOfToday = new DateTime((string)$year . "-" . (string)$month . "-" . (string)$day . " 00:00:00");
        $prevHour = clone $endOfToday;
        $prevHour->modify('-1 hour');

        // echo("End of Today: " . $endOfToday->format('Y-m-d H:i:s') . "<br>");
        // echo("End of Today - 1 hour: " . $prevHour->format('Y-m-d H:i:s') . "<br>");

        $hourArray = [];

        for($i = 0; $i < 24; $i++)
        {
            /** output the times to see if I overshot how many times to iterate */
            // echo("End of Today: " . $endOfToday->format('Y-m-d H:i:s') . "<br>");
            // echo("End of Today - 1 hour: " . $prevHour->format('Y-m-d H:i:s') . "<br>");

            // get the number of walkers in between the 2 times
            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" .
                $prevHour->format('Y-m-d H:i:s') . "\" AND \"" . $endOfToday->format('Y-m-d H:i:s') .
                "\" AND InOrOut=1";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            // push the result onto the array
            array_push($hourArray, $row['COUNT(WalkerNumber)']);

            // decrement the hour for the next iteration
            $endOfToday->modify('-1 hour');
            $prevHour->modify('-1 hour');
        }

        // echo("Numbers by hour: ");
        /* foreach (array_reverse($hourArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        echo("<br><br>"); */

        // reverse the array since it starts from the end of the day
        return array_reverse($hourArray);
    }

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
    public function getTrafficTimeRange($year1, $month1, $day1, $year2, $month2, $day2)
    {
        $startDay = new DateTime((string)$year1 . "-" . (string)$month1 . "-" . (string)$day1 . " 00:00:00");
        // get the next day to get the correct date
        $day2 += 1;
        // get the time of the absolute end of the end day
        $endDay = new DateTime((string)$year2 . "-" . (string)$month2 . "-" . (string)$day2 . " 00:00:00");


        // echo("Start Day: " . $startDay->format('Y-m-d H:i:s') . "<br>");
        // echo("End Day: " . $endDay->format('Y-m-d H:i:s') . "<br>");

        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" .
            $startDay->format('Y-m-d H:i:s') . "\" AND \"" . $endDay->format('Y-m-d H:i:s') .
            "\" AND InOrOut=1";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
        $row = $rs->fetch(PDO::FETCH_ASSOC);

        // echo("Number of People in given time range: " . $row['COUNT(WalkerNumber)'] . "<br><br>");

        return (int)$row['COUNT(WalkerNumber)'];
    }
}


//TODO: get number of walkers for a given week
?>


