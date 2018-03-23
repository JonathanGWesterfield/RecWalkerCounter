<?php

include_once "CommonInterface.php";
include_once "DBInterface.php";

$thisCommon = new Common(true);
$db = new DBAPI($thisCommon);

function print_array($values) {
    $printed_first = false;
    $result = "[";
    foreach($values as $v) {
        if($printed_first) {
            $result = $result . ",";
        }
        
        $result = $result . $v;
        $printed_first = true;
    }
    $result = $result . "]";
    return $result;
}

function display_graph($labels, $values, $date_format) { ?>
<canvas id="graph"></canvas>
<script>
var ctx = document.getElementById('graph').getContext('2d');
var chart = new Chart(ctx, {
    // The type of chart we want to create
    type: 'line',

    // The data for our dataset
    data: {
        labels: <?php echo $labels; ?>,
        datasets: [{
            label: "Number of walkers",
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, .08)',
            data: <?php echo print_array($values); ?>,
            lineTension: 0
        }]
    },

    // Configuration options go here
    options: {
        scales: {
            yAxes: [{
                stepSize: 1
            }]
        },

        legend: {
            display: false
        },

        title: {
            display: true,
            text: '<?php echo $date_format; ?>'
        },

        tooltips: {
            intersect: false
        }
    }
});
</script>
<?php }

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

            $labels = '["00:00", "01:00", "02:00", "03:00", "04:00", "05:00", "06:00", "07:00", "08:00", "09:00",' .
                '"10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00", "18:00", "19:00",' .
                '"20:00", "21:00", "22:00", "23:00"]';
            display_graph($labels, $db->getTrafficByDay($year, $month, $day), $dateObj->format('D, F j, Y'));

        } else if(strtolower($_GET['mode']) == 'month') {

            $labels = print_array(range(1,cal_days_in_month(CAL_GREGORIAN, $month, $year)));
            display_graph($labels, $db->getTrafficByMonth($year, $month), $dateObj->format('F Y'));

        } else if(strtolower($_GET['mode']) == 'year') {

            $labels = '["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"]';
            display_graph($labels, $db->getTrafficByYear($year), $dateObj->format('Y'));

        }
    }
    
} else {

    echo '<script>console.log("Could not load form with mode \\"' . $_GET['mode'] . '\\".");</script>';

} ?>

</script>