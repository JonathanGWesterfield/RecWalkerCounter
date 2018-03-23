<?php
include_once "CommonInterface.php";
include_once "DBInterface.php";

$thisCommon = new Common(true);
$db = new DBAPI($thisCommon);

function display_stats($date_format, $sub_division, $data) {
    echo "<h3>Statistics for <u>" . $date_format . "</u></h3>";
    echo "<div class=\"stat\"><div class=\"stat-contents\"><div>Total Walkers</div><div>" . array_sum($data) . "</div></div></div>";
    echo "<div class=\"stat\"><div class=\"stat-contents\"><div>Average Number of Walkers per " . $sub_division . "</div><div>" . round(array_sum($data)/count($data), 2) . "</div></div></div>";
    echo "<div class=\"stat\"><div class=\"stat-contents\"><div>Max Number of Walkers in an " . $sub_division . "</div><div>" . max($data) . "</div></div></div>";
    echo "<div class=\"stat\"><div class=\"stat-contents\"><div>Min Number of Walkers in an " . $sub_division . "</div><div>" . min($data) . "</div></div></div>";
}

if(!isset($_GET['mode'])) {

    echo '<script>console.log("Please specify a mode.");</script>';

} elseif(strtolower($_GET['mode']) == 'day' || strtolower($_GET['mode']) == 'month'|| strtolower($_GET['mode']) == 'year') {

    if(!isset($_GET['day']) || !isset($_GET['month']) || !isset($_GET['year'])) {

        echo '<script>console.log("Please specify a day, month, and year.");</script>';

    } else {

        $dateObj = DateTime::createFromFormat('d-m-Y|', $_GET['day'] . '-' . $_GET['month'] . '-' . $_GET['year']);
        $day   = intval($_GET['day']);
        $month = intval($_GET['month']);
        $year  = intval($_GET['year']);

        if(strtolower($_GET['mode']) == 'day') {

            display_stats($dateObj->format('D, F j, Y'), 'hour', $db->getTrafficByDay($year, $month, $day));

        } elseif(strtolower($_GET['mode']) == 'month') {

            display_stats($dateObj->format('F Y'), 'day', $db->getTrafficByMonth($year, $month));

        } elseif(strtolower($_GET['mode']) == 'year') {

            display_stats($dateObj->format('Y'), 'month', $db->getTrafficByYear($year));
            
        }

    }
} else {
    echo '<script>console.log("Could not load form with mode \\"' . $_GET['mode'] . '\\".");</script>';
}
?>