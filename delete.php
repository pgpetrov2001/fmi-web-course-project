<?php

require_once 'database.php';

$db = new Db();

$courseid = $_GET["courseid"];

$db->deleteCourse($courseid);

?>
