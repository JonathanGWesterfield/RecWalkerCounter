<?php

/** @file index.php
*/

    include_once "CommonInterface.php";
    include_once "DBInterface.php";

    $thisCommon = new Common(true);
    $db = new DBAPI($thisCommon);
?><!DOCTYPE html>
<html>
<head>

<title>Rec Foot Traffic</title>
<script src="Chart.min.js"></script>
<script src="jquery-3.3.1.min.js"></script>
<link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

<h1>Student Recreation Center South Entrance <br>Foot Traffic </h1>

<!-- Display a graph with controls. -->
<div id="left-container"><div id="left-contents">
<div id="graph-container"></div>
<form id="graph-control">
    <label for="mode-select">View by:</label>
    <select id="mode-select" name="mode-select">
        <option text="Day" value="Day">Day</option>
        <option value="Month">Month</option>
        <option value="Year" selected="selected">Year</option>
    </select>
    <label for="date-select">Date:</label>
    <input type="date" id="date-select" name="date-select" />
</form>
</div></div>

<!-- Display statistics -->
<div id="right-container"><div id="right-contents">
<div id="stats-container"></div>
</div></div>

<script>
function updateGraphStats() {
    var date = new Date($("#date-select").val());
    $("#graph-container").load("graph.php?mode=" + $("#mode-select option:checked").val() + "&day=" + date.getUTCDate() + "&month=" + (date.getUTCMonth()+1) + "&year=" + date.getUTCFullYear());
    $("#stats-container").load("stats.php?mode=" + $("#mode-select option:checked").val() + "&day=" + date.getUTCDate() + "&month=" + (date.getUTCMonth()+1) + "&year=" + date.getUTCFullYear());
}

$(document).ready(function(){
    var date = new Date();
    $("#date-select").val(date.getFullYear() + "-" + ("0000" + (date.getMonth()+1)).slice(-2) + "-" + ("0000" + date.getDate()).slice(-2));
    updateGraphStats();
    $("#graph-control").change(function() {
        if($("#date-select").val() != "")
            updateGraphStats();
    });
});
</script>

</body>
</html>
