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

    public function __construct($db)
    {
        echo("<br>Constructor has been called<br>");
        $this->COMMON = $db;
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

    public function getCurrentYearTraffic()
    {
        // TODO: Implement getCurrentYearTraffic() method.

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

            echo("The numbers for this year starting from the end of the year: ");
        }

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

    public function getCurrentMonthTraffic()
    {
        // TODO: Implement getCurrentMonthTraffic() method.
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