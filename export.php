<?php

require_once 'database.php';

$db = new Db();

$courseid = $_GET["courseid"];

$course = $db->getCourse($courseid);

$minorsNames = array_map(function ($minor) { return $minor['name']; }, $course['minors']);

$result = array(
	"Дисциплина" => $course["name"],
	"Специалности" => [array_combine(
		$minorsNames,
		array_fill(0, count($course['minors']), array())
	)],
	"Преподавател" => $course["lecturer"],
	"Кредити" => $course["credits"],
	"Анотация" => $course["headnote"],
	"Предварителни изисквания" => $course["prerequisites"],
	"Очаквани резултати" => $course["expected_results"],
	"Съдържание" => array_map(function ($item) { return $item['description']; }, $course['synopsis']),
	"Конспект" => array_map(function ($item) { return $item['description']; }, $course['exam_synopsis']),
	"Библиография" => array_map(function ($item) { return $item['content']; }, $course['bibliography']),
	"Зависи от" => array_map(function ($item) { return $item['name']; }, $course['course_parents']),
	"Зависят от нея" => array_map(function ($item) { return $item['name']; }, $course['course_children']),
);

header('Content-Type: application/json');
echo json_encode($result);

?>
