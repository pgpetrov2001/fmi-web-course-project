<?php

require_once 'config.php';

class Db {
    private $connection;
    public function __construct() {
		$DB_HOST = DB_HOST;
        $DB_NAME = DB_NAME;

        $this->connection = new PDO("mysql:host=$DB_HOST;port=3306;dbname=$DB_NAME", DB_USER, DB_PASS, [
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8",
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    public function getConnection() {
        return $this->connection;
    }

	public function getAll($object) {
		$sql = "SELECT * FROM ".$object;
		$stmt = $this->getConnection()->query($sql);
		$items = $stmt->fetchAll();
		return $items;
	}

	public function deleteCourse($id) {
		$sql = <<<SQL
			DELETE FROM courses WHERE id = ?
		SQL;
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->execute(array($id));
		error_log("deleted course with id ".$id);
		return $courses;
	}

	public function getCourse($courseId) {
		$sql = "SELECT
			courses.*,
			CONCAT(lecturers.titles, ' ', lecturers.names) AS lecturer,
			departments.name AS department
			FROM courses
			JOIN lecturers ON lecturer_id = lecturers.id
			JOIN departments ON department_id = departments.id
			WHERE courses.id = ?";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->execute([$courseId]);
		$course = $stmt->fetch();
		$course['synopsis'] = $this->getSynopses()[$courseId] ?? array();
		$course['exam_synopsis'] = $this->getExamSynopses()[$courseId] ?? array();
		$course['bibliography'] = $this->getBibliographies()[$courseId] ?? array();
		$course['minors'] = $this->getMinors()[$courseId] ?? array();
		$course['course_parents'] = $this->getCourseParents($courseId) ?? array();
		$course['course_children'] = $this->getCourseChildren($courseId) ?? array();
		return $course;
	}

	public function getCourses() {
		$sql = "SELECT
			courses.*,
			lecture_engagement + seminar_engagement + practice_engagement + homework_engagement + test_prep_engagement +
			course_project_engagement + self_study_engagement + study_report_engagement + other_extracurricular_engagement +
			exam_prep_engagement AS total_engagement,
			CONCAT(lecturers.titles, ' ', lecturers.names) AS lecturer, departments.name AS department
			FROM courses
			JOIN lecturers ON lecturer_id = lecturers.id
			JOIN departments ON department_id = departments.id";
		$stmt = $this->getConnection()->query($sql);
		$courses = $stmt->fetchAll();
		error_log($courses[0]['total_engagement']);
		return $courses;
	}

	public function getSynopses() {
		$sql = "SELECT * FROM synopses ORDER BY course_id, position";
		$stmt = $this->getConnection()->query($sql);
		$result = $stmt->fetchAll();
		$data = array();
		foreach ($result as $item) {
			$key = $item['course_id'];
			if (!isset($data[$key])) {
				$data[$key] = array();
			}
			array_push($data[$key], array(
				'description' => $item['description'],
				'horarium_lectures' => $item['horarium_lectures'],
				'horarium_seminars' => $item['horarium_seminars'],
				'horarium_practice' => $item['horarium_practice']
			));
		}
		return $data;
	}

	public function getExamSynopses() {
		$sql = "SELECT * FROM exams_synopses ORDER BY course_id, position";
		$stmt = $this->getConnection()->query($sql);
		$result = $stmt->fetchAll();
		$data = array();
		foreach ($result as $item) {
			$key = $item['course_id'];
			if (!isset($data[$key])) {
				$data[$key] = array();
			}
			array_push($data[$key], array(
				'position' => $item['position'],
				'description' => $item['description'],
			));
		}
		return $data;
	}

	public function getBibliographies() {
		$sql = "SELECT * FROM bibliographies ORDER BY course_id, position";
		$stmt = $this->getConnection()->query($sql);
		$result = $stmt->fetchAll();
		$data = array();
		foreach ($result as $item) {
			$key = $item['course_id'];
			if (!isset($data[$key])) {
				$data[$key] = array();
			}
			array_push($data[$key], array(
				'position' => $item['position'],
				'content' => $item['content'],
			));
		}
		return $data;
	}

	public function getMinors() {
		$sql = "SELECT * FROM courses_minors JOIN minors ON minor_id = minors.id ORDER BY course_id";
		$stmt = $this->getConnection()->query($sql);
		$result = $stmt->fetchAll();
		$data = array();
		foreach ($result as $item) {
			$key = $item['course_id'];
			if (!isset($data[$key])) {
				$data[$key] = array();
			}
			array_push($data[$key], array(
				'name' => $item['name'],
				'abbreviation' => $item['abbreviation'],
			));
		}
		return $data;
	}

	public function getCourseParents($courseId) {
		$sql = "SELECT courses.*
			FROM course_dependencies
			JOIN courses ON parent_id = courses.id
			WHERE child_id = ?";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->execute([$courseId]);
		$result = $stmt->fetchAll();
		$data = array();
		foreach ($result as $item) {
			array_push($data, $item);
		}
		return $data;
	}

	public function getCourseChildren($courseId) {
		$sql = "SELECT courses.*
			FROM course_dependencies
			JOIN courses ON child_id = courses.id
			WHERE parent_id = ?";
		$stmt = $this->getConnection()->prepare($sql);
		$stmt->execute([$courseId]);
		$result = $stmt->fetchAll();
		$data = array();
		foreach ($result as $item) {
			array_push($data, $item);
		}
		return $data;
	}

	public function getCoursesParents() {
		$sql = "SELECT * FROM course_dependencies ORDER BY child_id";
		$stmt = $this->getConnection()->query($sql);
		$result = $stmt->fetchAll();
		$data = array();
		foreach ($result as $item) {
			$key = $item['child_id'];
			if (!isset($data[$key])) {
				$data[$key] = array();
			}
			array_push($data[$key], $item['parent_id']);
		}
		return $data;
	}

	public function getCourseDependencies() {
		global $db;
		$sql = "SELECT
			parent_courses.name AS parent_name,
			child_courses.name AS child_name
			FROM course_dependencies
			JOIN courses AS parent_courses ON parent_courses.id = course_dependencies.parent_id
			JOIN courses AS child_courses ON child_courses.id = course_dependencies.child_id;";
		$stmt = $db->getConnection()->query($sql);
		$courses = $stmt->fetchAll();
		return $courses;
	}

	public function addCourse($data) {
		$courseName = $data["courseName"];
		$lecturer_id = $data["lecturer"];
		$credits = $data["credits"];
		$lectureEngagement = $data["lectureEngagement"];
		$seminarEngagement = $data["seminarEngagement"];
		$practiceEngagement = $data["practiceEngagement"];
		$homeworkEngagement = $data["homeworkEngagement"];
		$testPrepEngagement = $data["testPrepEngagement"];
		$courseProjectEngagement = $data["courseProjectEngagement"];
		$selfStudyEngagement = $data["selfStudyEngagement"];
		$studyReportEngagement = $data["studyReportEngagement"];
		$otherExtracurricularEngagement = $data["otherExtracurricularEngagement"];
		$examPrepEngagement = $data["examPrepEngagement"];
		$courseProjectGradePercentage = $data["courseProjectGradePercentage"];
		$testsGradePercentage = $data["testsGradePercentage"];
		$examGradePercentage = $data["examGradePercentage"];
		$headnote = $data["headnote"];
		$prerequisites = $data["prerequisites"];
		$expectedResults = $data["expectedResults"];

		$sql = "INSERT INTO courses
				(name, lecturer_id, credits, lecture_engagement, seminar_engagement, practice_engagement, homework_engagement, 
				test_prep_engagement, course_project_engagement, self_study_engagement, study_report_engagement, 
				other_extracurricular_engagement, exam_prep_engagement, course_project_grade_percentage, 
				tests_grade_percentage, exam_grade_percentage, headnote, prerequisites, expected_results, 
				created_at, updated_at)
				VALUES 
				(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

		$stmt = $this->getConnection()->prepare($sql);
		$stmt->execute([$courseName, $lecturer_id, $credits, $lectureEngagement, $seminarEngagement, $practiceEngagement,
			$homeworkEngagement, $testPrepEngagement, $courseProjectEngagement, $selfStudyEngagement,
			$studyReportEngagement, $otherExtracurricularEngagement, $examPrepEngagement,
			$courseProjectGradePercentage, $testsGradePercentage, $examGradePercentage, $headnote,
			$prerequisites, $expectedResults]);
		error_log("created course successfully");
	}

	public function addSynopsis($courseId, $data) {
		$synopsisPosition = $data["synopsisPosition"];
		$synopsisDescription = $data["synopsisDescription"];
		$synopsisHorariumLectures = $data["synopsisHorariumLectures"];
		$synopsisHorariumSeminars = $data["synopsisHorariumSeminars"];
		$synopsisHorariumPractice = $data["synopsisHorariumPractice"];

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
		$stmt = $this->getConnection()->prepare($select);
		$stmt->execute($values);
		error_log('added synopsis successfully');
	}

	public function addExamSynopsis($courseId, $data) {
		$examSynopsisPosition = $data["examSynopsisPosition"];
		$examSynopsisDescription = $data["examSynopsisDescription"];
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
		$stmt = $this->getConnection()->prepare($select);
		$stmt->execute($values);
		error_log('added exam synopsis successfully');
	}

	public function addCourseMinors($courseId, $data) {
		if (!array_key_exists("courseMinors", $data)) {
			return;
		}
		$courseMinors = $data["courseMinors"];
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

		$stmt = $this->getConnection()->prepare($select);
		$stmt->execute($values);

		error_log('added course minors successfully');
	}

	public function addParentCourses($courseId, $data) {
		if (!array_key_exists("parentCourses", $data)) {
			return;
		}
		$parentCourses = $data["parentCourses"];
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
		$stmt = $this->getConnection()->prepare($select);
		$stmt->execute($values);
		error_log('added parent courses successfully');
	}

	public function addBibliography($courseId, $data) {
		$bibliographyPosition = $data["bibliographyPosition"];
		$bibliographyContent = $data["bibliographyContent"];
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
		$stmt = $this->getConnection()->prepare($select);
		$stmt->execute($values);
		error_log('added course bibliography successfully');
	}

}
