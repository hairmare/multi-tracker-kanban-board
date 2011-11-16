<?php

$query = $_GET['q'];
$callback = $_GET['callback'];
header('Content-Type:application/javascript; charset=utf-8');

$project1 = new stdClass;
$project1->id = "1";
$project1->name = "Project 1";
$project2 = new stdClass;
$project2->id = "2";
$project2->name = "Project 2";

$data = new stdClass;
$data->projects = array(
    $project1,
    $project2
);

echo $callback."(".json_encode($data).");";


