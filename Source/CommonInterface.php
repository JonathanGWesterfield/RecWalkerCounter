<?php
/**
 * Created by PhpStorm.
 * Date: 2/8/18
 * Time: 4:31 PM
 *
 * This is an interface for Common.
 */

include_once "./CommonMethods.php";

interface CommonInterface
{
    public function Common($debug);
    public function connect($db);
    public function executeQuery($sql, $filename);
}