<?php

require_once 'database.php';

$db = new Db();

function addCourse() {
	global $db;
    $courseName = $_POST["courseName"];
	$lecturer_id = $_POST["lecturer"];
    $credits = $_POST["credits"];
    $lectureEngagement = $_POST["lectureEngagement"];
    $seminarEngagement = $_POST["seminarEngagement"];
    $practiceEngagement = $_POST["practiceEngagement"];
    $homeworkEngagement = $_POST["homeworkEngagement"];
    $testPrepEngagement = $_POST["testPrepEngagement"];
    $courseProjectEngagement = $_POST["courseProjectEngagement"];
    $selfStudyEngagement = $_POST["selfStudyEngagement"];
    $studyReportEngagement = $_POST["studyReportEngagement"];
    $otherExtracurricularEngagement = $_POST["otherExtracurricularEngagement"];
    $examPrepEngagement = $_POST["examPrepEngagement"];
    $courseProjectGradePercentage = $_POST["courseProjectGradePercentage"];
    $testsGradePercentage = $_POST["testsGradePercentage"];
    $examGradePercentage = $_POST["examGradePercentage"];
    $headnote = $_POST["headnote"];
    $prerequisites = $_POST["prerequisites"];
    $expectedResults = $_POST["expectedResults"];

	$sql = "INSERT INTO courses
			(name, lecturer_id, credits, lecture_engagement, seminar_engagement, practice_engagement, homework_engagement, 
			test_prep_engagement, course_project_engagement, self_study_engagement, study_report_engagement, 
			other_extracurricular_engagement, exam_prep_engagement, course_project_grade_percentage, 
			tests_grade_percentage, exam_grade_percentage, headnote, prerequisites, expected_results, 
			created_at, updated_at)
			VALUES 
			(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

	$stmt = $db->getConnection()->prepare($sql);
	$stmt->execute([$courseName, $lecturer_id, $credits, $lectureEngagement, $seminarEngagement, $practiceEngagement,
		$homeworkEngagement, $testPrepEngagement, $courseProjectEngagement, $selfStudyEngagement,
		$studyReportEngagement, $otherExtracurricularEngagement, $examPrepEngagement,
		$courseProjectGradePercentage, $testsGradePercentage, $examGradePercentage, $headnote,
		$prerequisites, $expectedResults]);
	error_log("created course successfully");
}

function addSynopsis($courseId) {
	global $db;
	$synopsisPosition = $_POST["synopsisPosition"];
	$synopsisDescription = $_POST["synopsisDescription"];
	$synopsisHorariumLectures = $_POST["synopsisHorariumLectures"];
	$synopsisHorariumSeminars = $_POST["synopsisHorariumSeminars"];
	$synopsisHorariumPractice = $_POST["synopsisHorariumPractice"];

	$values = array_fill(0, count($synopsisPosition)*6, '');
	$in = '';
	for ($i = 0; $i < count($synopsisPosition); $i++) {
		$values[6*$i+0] = $courseId;
		$values[6*$i+1] = $synopsisPosition[$i];
		$values[6*$i+2] = $synopsisDescription[$i];
		$values[6*$i+3] = $synopsisHorariumLectures[$i];
		$values[6*$i+4] = $synopsisHorariumSeminars[$i];
		$values[6*$i+5] = $synopsisHorariumPractice[$i];
		if ($i + 1 < count($synopsisPosition)) {
			$in .= "(?, ?, ?, ?, ?, ?),\n";
		} else {
			$in .= "(?, ?, ?, ?, ?, ?)";
		}
	}
	$select = <<<SQL
		INSERT INTO synopses
		(course_id, position, description, horarium_lectures, horarium_seminars, horarium_practice)
		VALUES
		$in
	SQL;
	$stmt = $db->getConnection()->prepare($select);
	$stmt->execute($values);
	error_log('added synopsis successfully');
}

function addExamSynopsis($courseId) {
	global $db;
	$examSynopsisPosition = $_POST["examSynopsisPosition"];
	$examSynopsisDescription = $_POST["examSynopsisDescription"];
	$values = array_fill(0, count($examSynopsisPosition)*3, '');
	$in = '';
	for ($i = 0; $i < count($examSynopsisPosition); $i++) {
		$values[3*$i+0] = $courseId;
		$values[3*$i+1] = $examSynopsisPosition[$i];
		$values[3*$i+2] = $examSynopsisDescription[$i];
		if ($i + 1 < count($examSynopsisPosition)) {
			$in .= "(?, ?, ?),\n";
		} else {
			$in .= "(?, ?, ?)";
		}
	}
	$select = <<<SQL
		INSERT INTO exams_synopses
		(course_id, position, description)
		VALUES
		$in
	SQL;
	$stmt = $db->getConnection()->prepare($select);
	$stmt->execute($values);
	error_log('added exam synopsis successfully');
}

function addCourseMinors($courseId) {
	global $db;
	$courseMinors = $_POST["courseMinors"];
	if (count($courseMinors) == 0) {
		return;
	}
	$values = array_fill(0, count($courseMinors)*2, '');
	$in = '';
	for ($i = 0; $i < count($courseMinors); $i++) {
		$values[2*$i+0] = $courseId;
		$values[2*$i+1] = $courseMinors[$i];
		if ($i + 1 < count($courseMinors)) {
			$in .= "(?, ?),\n";
		} else {
			$in .= "(?, ?)";
		}
	}
	$select = <<<SQL
		INSERT INTO courses_minors
		(course_id, minor_id)
		VALUES
		$in
	SQL;

	$stmt = $db->getConnection()->prepare($select);
	$stmt->execute($values);

	error_log('added course minors successfully');
}

function addParentCourses($courseId) {
	global $db;
	if (!array_key_exists("parentCourses", $_POST)) {
		return;
	}
	$parentCourses = $_POST["parentCourses"];
	$values = array_fill(0, count($parentCourses)*2, '');
	$in = '';
	for ($i = 0; $i < count($parentCourses); $i++) {
		$values[2*$i+0] = $courseId;
		$values[2*$i+1] = $parentCourses[$i];
		if ($i + 1 < count($parentCourses)) {
			$in .= "(?, ?),\n";
		} else {
			$in .= "(?, ?)";
		}
	}
	$select = <<<SQL
		INSERT INTO course_dependencies
		(child_id, parent_id)
		VALUES
		$in
	SQL;
	$stmt = $db->getConnection()->prepare($select);
	$stmt->execute($values);
	error_log('added parent courses successfully');
}

function addBibliography($courseId) {
	global $db;
	$bibliographyPosition = $_POST["bibliographyPosition"];
	$bibliographyContent = $_POST["bibliographyContent"];
	if (count($bibliographyPosition) == 0) {
		return 0;
	}
	$values = array_fill(0, count($bibliographyPosition)*3, '');
	$in = '';
	for ($i = 0; $i < count($bibliographyPosition); $i++) {
		$values[3*$i+0] = $courseId;
		$values[3*$i+1] = $bibliographyPosition[$i];
		$values[3*$i+2] = $bibliographyContent[$i];
		if ($i + 1 < count($bibliographyPosition)) {
			$in .= "(?, ?, ?),\n";
		} else {
			$in .= "(?, ?, ?)";
		}
	}
	$select = <<<SQL
		INSERT INTO bibliographies
		(course_id, position, content)
		VALUES
		$in
	SQL;
	$stmt = $db->getConnection()->prepare($select);
	$stmt->execute($values);
	error_log('added course bibliography successfully');
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Insert data into the database
    try {
		$db->getConnection()->beginTransaction();

		addCourse();
		$courseId = $db->getConnection()->lastInsertId();
		addSynopsis($courseId);
		addExamSynopsis($courseId);
		addCourseMinors($courseId);
		addParentCourses($courseId);
		addBibliography($courseId);

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
