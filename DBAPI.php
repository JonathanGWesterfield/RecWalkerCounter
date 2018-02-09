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

    public function __construct($db)
    {
        echo("<br>Constructor has been called<br>");
        $this->COMMON = $db;


    }

    public function __destruct()
    {
        // TODO: Implement __destruct() method.
        echo("<br>Destructor has been called <br>");
    }

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
    public function getTotalNumWalkers()
    {
        $sql = "SELECT COUNT(WalkerNumber) FROM WalkerData";

        $rs = $this->COMMON->executeQuery($sql, $_SERVER["SCRIPT_NAME"]);

        $row = $rs->fetch(PDO::FETCH_ASSOC);

        echo("Total Num Walkers is: " . $row['COUNT(WalkerNumber)'] . "<br>");
    }
}

?>