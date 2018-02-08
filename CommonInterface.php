<?php
/**
 * Created by PhpStorm.
 * User: JonathanWesterfield
 * Date: 2/8/18
 * Time: 4:31 PM
 */

include_once "./CommonMethods.php";

interface CommonInterface
{
    public function Common($debug);
    public function connect($db);
    public function executeQuery($sql, $filename);
}