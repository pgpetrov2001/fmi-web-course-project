<?php

require_once 'database.php';

$db = new Db();

function deleteCourse($id) {
	global $db;
	$sql = <<<SQL
		DELETE FROM courses WHERE id = ?
	SQL;
    $stmt = $db->getConnection()->prepare($sql);
	$stmt->execute(array($id));
	error_log("deleted course with id ".$id);
	return $courses;
}

$courseid = $_GET["courseid"];

deleteCourse($courseid);

?>
