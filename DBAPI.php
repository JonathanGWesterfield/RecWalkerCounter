<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 4:35 PM
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
        echo("<br>Constructor has been called<br>");

        $this->COMMON = $db;

        date_default_timezone_set('America/Chicago');

        // Check to make sure the timezone the server is in is set correctly (to College Station)
        if (date_default_timezone_get() != 'America/Chicago')
        {
            echo("ERROR! TIMEZONE SET INCORRECTLY!!! NOT SET TO COLLEGE STATION'S TIME (CHICAGO)!!<br>");
            echo 'date_default_timezone_set: ' . date_default_timezone_get() . '<br/>';
            exit(1);
        }
        echo("Default Timezone set to: " . date_default_timezone_get() . "<br>");

        $this->currentDate = date("Y-m-d");
        $this->currentDateTime = date("Y-m-d H:i:s");
        $this->currentWeekDay = date('w');
        $this->tomorrowDate = date('Y-m-d', strtotime('+1 day'));
        $this->currentYear = date("Y");
        $this->currentMonth = date("m");
        $this->nextYear = date('Y', strtotime('+1 year'));
        $this->nextYear = $this->nextYear . "-01-01";

        echo("Current Date: " . $this->currentDate . "<br>");
        echo("Current DateTime: " . $this->currentDateTime . "<br>");
        echo("Current weekday numerically: " . $this->currentWeekDay . "<br>");
        echo("Tomorrow's Date: " . $this->tomorrowDate . "<br>");
        echo("The current year is: " . $this->currentYear . "<br>");
        echo("The current month is: " . $this->currentMonth . "<br>");
        echo("Next year is: " . $this->nextYear . "<br>");
        echo("<br><br>");
    }

    /**
     * Class destructor
     */
    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        echo("<br>Destructor has been called <br>");
        echo("DESTROYING EVERTYING<br>");
    }

    /**
     * Prints out a table of the entire database
     */
    public function printEntireDB()
    {
        $sql = "SELECT * FROM WalkerData ORDER BY WalkerNumber";

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
        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        echo("Total Num Walkers is: " . $row['COUNT(WalkerNumber)'] . "<br>");

        return (int)$row['COUNT(WalkerNumber)'];
    }

    /**
     * @return int
     *
     * Gets the number of walkers from Today
     */
    public function getNumWalkersToday()
    {
        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" . $this->currentDate . "%\" AND \""
            . $this->tomorrowDate . "%\"";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        echo("Total Num Walkers is: " . $row['COUNT(WalkerNumber)'] . "<br>");

        return (int)$row['COUNT(WalkerNumber)'];
    }

    /**
     * @return int
     *
     * Gets the number of walkers in the last week starting from today
     */
    public function getNumWalkersThisWeek()
    {
        $lastWeek = date('Y-m-d', strtotime('-1 week'));

        echo("Last weeks date: " . $lastWeek . "<br>");

        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" . $lastWeek . "%\" AND \""
            . $this->tomorrowDate . "%\"";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        echo("Total Num Walkers is: " . $row['COUNT(WalkerNumber)'] . "<br>");

        return (int)$row['COUNT(WalkerNumber)'];
    }

    /**
     * @return array
     *
     * Returns an array of the traffic numbers for each month in a 12 element array
     */
    public function getCurrentYearTraffic()
    {
        echo("<br>Starting getCurrentYearTraffic() <br>");

        $prevMonth = new DateTime($this->nextYear);

        $monthArray = []; // array for the numbers for the months

        for($i = 0; $i < 12; $i++) // get numbers for each month
        {
            $lookMonth = clone $prevMonth;
            $prevMonth->modify('-1 month'); // decrement month
            echo $prevMonth->format('Y-m-d');

            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" . $prevMonth->format('Y-m-d') . "%\" AND \"" .
                $lookMonth->format('Y-m-d') . "%\"";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            array_push($monthArray, $row['COUNT(WalkerNumber)']); // add the result to the month array
        }

        echo("The numbers for this year starting from the end of the year: ");
        foreach ($monthArray as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        return array_reverse($monthArray); // reverse the array to start in January instead of December
    }

    public function getTrafficByYear($year)
    {
        // TODO: Implement getTrafficByYear() method.
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
        echo("The beginning of this month: " . $thisMonth->format('Y-m-d') . "<br>");

        // copy to make the next Month's object
        $nextMonth = clone $thisMonth;

        // get the next month's date by incrementing a month
        $nextMonth->modify('+1 month');
        echo("Next Month is: " . $nextMonth->format('Y-m-d') . "<br>");

        // calculate the # of days between the start and end of the month - # of days in the month
        $diff = $nextMonth->diff($thisMonth)->format("%a");

        echo("Difference between the 2 months in days: " . $diff . "<br>");

        // create 2 days to look at the numbers between each day
        $lookDay = clone $nextMonth;
        $dayBefore = clone $nextMonth;
        $dayBefore->modify('-1 day');

        echo("Look day: " . $lookDay->format('Y-m-d') . "<br>");
        echo("Day Before: " . $dayBefore->format('Y-m-d') . "<br>");

        $dayArray = [];

        for($i = 0; $i < $diff; $i++)
        {
            $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData WHERE DateTime BETWEEN \"" .
                $dayBefore->format('Y-m-d') . "%\" AND \"" . $lookDay->format('Y-m-d') . "%\"";

            $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);
            $row = $rs->fetch(PDO::FETCH_ASSOC);

            // add the result to the day array
            array_push($dayArray, $row['COUNT(WalkerNumber)']);

            // decrement the target days for the next iteration
            $lookDay->modify("-1 day");
            $dayBefore->modify("-1 day");
        }

        echo("Numbers for the days of this current month: <br>");
        foreach (array_reverse($dayArray) as $element)
        {
            echo($element . " "); // print out the array (will be starting from December to January)
        }

        // return the days reversed since the original array starts from the end of the month
        return array_reverse($dayArray);
    }

    public function getTrafficByMonth($year, $month)
    {
        // TODO: Implement getTrafficByMonth() method.
    }

    public function getCurrentDayTraffic()
    {
        // TODO: Implement getCurrentDayTraffic() method.
    }

    public function getTrafficByDay($year, $month, $day)
    {
        // TODO: Implement getTrafficByDay() method.
    }
}

?>