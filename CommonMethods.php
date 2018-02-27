<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 4:20 PM
 */

class Common implements CommonInterface
{
    var $conn;
    var $debug;

    /* // info for local DB
    var $db="localhost:8889";
    var $dbname="WalkerProject";
    var $user="root";
    var $pass="root"; */

    var $db="database.cse.tamu.edu";
    var $dbname="WalkerData";
    var $user="jgwesterfield";
    var $pass="Whoop19!";

    function Common($debug)
    {
        $this->debug = $debug;
        $rs = $this->connect($this->user); // db name really here
        return $rs;
    }

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% */

    function connect($db)// connect to MySQL DB Server
    {
        try
        {
            $this->conn = new PDO('mysql:host='.$this->db.';dbname='.$this->dbname, $this->user, $this->pass);
        }
        catch (PDOException $e)
        {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        }
    }

// %%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%% */

    function executeQuery($sql, $filename) // execute query
    {
        if($this->debug == true) { echo("<br>$sql <br>\n"); }
        $rs = $this->conn->query($sql) or die("Could not execute query '$sql' in $filename");
        return $rs;
    }

} // ends class, NEEDED!!

?>
