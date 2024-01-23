<?php

require_once 'database.php';

$db = new Db();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insert data into the database
    try {
		$db->getConnection()->beginTransaction();

		$db->addCourse($_POST);
		$courseId = $db->getConnection()->lastInsertId();
		$db->addSynopsis($courseId, $_POST);
		$db->addExamSynopsis($courseId, $_POST);
		$db->addCourseMinors($courseId, $_POST);
		$db->addParentCourses($courseId, $_POST);
		$db->addBibliography($courseId, $_POST);

		$db->getConnection()->commit();

		error_log("Created course and all its dependencies successfully.");
    } catch (PDOException $e) {
		$db->getConnection()->rollback();
		//in debug mode only:
		die("Error: " . $e->getMessage());
		error_log("Error: " . $e->getMessage());
	} catch (Exception $e) {
		$db->getConnection()->rollback();
		//in debug mode only:
		die("Error: " . $e->getMessage());
		error_log("Error: " . $e->getMessage());
	} finally {
		header("Location: index.php");
		exit();
	}
} else {
    // If the form is not submitted via POST, redirect to the form page
    header("Location: create_course.php");
    exit();
}
?>
