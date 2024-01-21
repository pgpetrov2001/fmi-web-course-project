<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Courses</title>
	<link rel="stylesheet" href="static/fontawesome/css/all.min.css">
    <style>
        /* Your CSS styles here */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
		h1 {
			text-align: center;
		}
        .course {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
		.curriculumContainer {
			display: flex;
			justify-content: space-between;
		}
		.delete-button {
			text-decoration: none;
			button {
				background-color: #ff6666;
				color: #fff; /* Set your desired text color */ 
				padding: 5px 5px; 
				border: none; 
				border-radius: 5px; 
				cursor: pointer; 
			}
        }
		nav {
			text-align: center;
			position: sticky;
			top: 0;
			z-index: 100;
			background-color: #3498db;
			box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Slightly larger box shadow with a more subtle color */
			border-bottom: 2px solid #2980b9; /* Add a bottom border */
			border-radius: 5px; /* Add rounded corners */

			ul {
				list-style: none;
				padding: 0;
				margin: 0;
				display: flex;
				white-space: nowrap; /* Prevent items from wrapping to the next line */
			}

			li {
				margin: 0;
			}

			a {
				text-decoration: none;
				color: #ecf0f1;
				padding: 15px;
				display: block;
				transition: background-color 0.3s ease;
				border-radius: 5px; /* Add rounded corners */
			}

			a:hover {
				background-color: #2980b9;
			}

			/* Adjustments for better spacing and appearance */
			li:not(:last-child) {
				margin-right: 10px;
			}
		}

    </style>
</head>

<?php

require_once 'database.php';

$db = new Db();

function getCourses() {
	global $db;
	$sql = "SELECT
		courses.*,
		lecture_engagement + seminar_engagement + practice_engagement + homework_engagement + test_prep_engagement +
		course_project_engagement + self_study_engagement + study_report_engagement + other_extracurricular_engagement +
		exam_prep_engagement AS total_engagement,
		CONCAT(lecturers.titles, ' ', lecturers.names) AS lecturer, departments.name AS department
		FROM courses
		JOIN lecturers ON lecturer_id = lecturers.id
		JOIN departments ON department_id = departments.id";
    $stmt = $db->getConnection()->query($sql);
    $courses = $stmt->fetchAll();
	error_log($courses[0]['total_engagement']);
	return $courses;
}

function getSynopses() {
	global $db;
    $sql = "SELECT * FROM synopses ORDER BY course_id, position";
    $stmt = $db->getConnection()->query($sql);
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

function getExamSynopses() {
	global $db;
    $sql = "SELECT * FROM exams_synopses ORDER BY course_id, position";
    $stmt = $db->getConnection()->query($sql);
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

function getBibliographies() {
	global $db;
    $sql = "SELECT * FROM bibliographies ORDER BY course_id, position";
    $stmt = $db->getConnection()->query($sql);
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

function getMinors() {
	global $db;
    $sql = "SELECT * FROM courses_minors JOIN minors ON minor_id = minors.id ORDER BY course_id";
    $stmt = $db->getConnection()->query($sql);
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

function getCoursesParents() {
	global $db;
    $sql = "SELECT * FROM course_dependencies ORDER BY child_id";
    $stmt = $db->getConnection()->query($sql);
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

// Fetch courses from the database
try {
	$courses = getCourses();
	$synopsisFromCourseId = getSynopses();
	$examSynopsisFromCourseId = getExamSynopses();
	$bibliographyFromCourseId = getBibliographies();
	$minorsFromCourseId = getMinors();
	$parentsFromCourseId = getCoursesParents();
} catch (PDOException $e) {
	//only in debug mode:
    die("Error: " . $e->getMessage());
	error_log("Error: " . $e->getMessage());
}

?>

<body>

<header>
	<h1>Course List</h1>
	<a href="create_course.php"><button>Create course</button></a>
	<a href="show_graph.php"><button>Show graph</button></a>
</header>

<nav>
	<ul>
		<?php foreach ($courses as $course): ?>
		<li><a href="#course-<?php echo $course['id']; ?>"><?php echo $course['name']; ?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>

<main>
	<?php if (!empty($courses)): ?>
		<?php foreach ($courses as $course): ?>
			<?php $synopsis = $synopsisFromCourseId[$course['id']] ?? array(); ?>
			<?php $examSynopsis = $examSynopsisFromCourseId[$course['id']] ?? array(); ?>
			<?php $bibliography = $bibliographyFromCourseId[$course['id']] ?? array(); ?>
			<?php $minors = $minorsFromCourseId[$course['id']] ?? array(); ?>
			<section class="course" id="course-<?php echo $course['id']; ?>">
				<div class="curriculumContainer" style="">
					<h2>Curriculum</h2>
					<a href="#" class="delete-button" data-courseid="<?php echo $course['id']; ?>">
						<button><i class="fa fa-trash"></i> Delete </button>
					</a>
				</div>
				<table>
					<thead> <th>Minors:</th> </thead>
					<tbody> <td><?php echo join(', ', array_map(fn($m) => $m['abbreviation'], $minors)) ?></td> </tbody>
				</table>
				<table>
					<thead> <th>Course:</th> </thead>
					<tbody> <td><?php echo $course['name']; ?></td> </tbody>
				</table>
				<table>
					<thead>
						<tr><th colspan="2">The curriculum has been developed and proposed for publishing by:</th></tr>
						<tr><th>Department:</th><th>Person:</th></tr>
					</thead>
					<tbody>
						<tr><td><?php echo $course['department']; ?></td><td><?php echo $course['lecturer']; ?></td></tr>
					</tbody>
				</table>
				<table>
					<tbody>
						<tr><td>Total Engagement</td> <td><?php echo $course['total_engagement']; ?></td></tr>
						<tr><td> Credits </td> <td><?php echo $course['credits']; ?></td></tr>
					</tbody>
				</table>
				<table>
					<thead>
						<tr><th>Engagement</th> <th>Type</th> <th>Horarium</th></tr>
					</thead>
					<tbody>
						<tr><td rowspan="3">On-site Engagement</td><td>Lectures</td><td><?php echo $course['lecture_engagement']; ?></td></tr>
						<tr><td>Seminars</td><td><?php echo $course['seminar_engagement']; ?></td></tr>
						<tr><td>Practice</td><td><?php echo $course['practice_engagement']; ?></td></tr>
						<tr><td rowspan="7">Off-site Engagement</td><td>Homework</td><td><?php echo $course['homework_engagement']; ?></td></tr>
						<tr><td>Tests Preparation</td><td><?php echo $course['test_prep_engagement']; ?></td></tr>
						<tr><td>Course Project</td><td><?php echo $course['course_project_engagement']; ?></td></tr>
						<tr><td>Self Study</td><td><?php echo $course['self_study_engagement']; ?></td></tr>
						<tr><td>Study Report</td><td><?php echo $course['study_report_engagement']; ?></td></tr>
						<tr><td>Other Extracurricular Engagement</td><td><?php echo $course['other_extracurricular_engagement']; ?></td></tr>
						<tr><td>Exam Preparation</td><td><?php echo $course['exam_prep_engagement']; ?></td></tr>
					</tbody>
				</table>
				<table>
					<thead>
						<tr><th colspan="2">Formation of course grade</th></tr>
					</thead>
					<tbody>
						<tr><td> Course Project </td> <td><?php echo $course['course_project_grade_percentage']; ?>%</td></tr>
						<tr><td> Tests </td> <td><?php echo $course['tests_grade_percentage']; ?>%</td></tr>
						<tr><td> Exam </td> <td><?php echo $course['exam_grade_percentage']; ?>%</td></tr>
					</tbody>
				</table>
				<table>
					<thead>
						<tr><th>Headnote</th></tr>
					</thead>
					<tbody>
						<tr><td><p><?php echo $course['headnote']; ?></p></td></tr>
					</tbody>
				</table>
				<table>
					<thead>
						<tr><th>Prerequsites</th></tr>
					</thead>
					<tbody>
						<tr><td><p><?php echo $course['prerequisites']; ?></p></td></tr>
					</tbody>
				</table>
				<table>
					<thead>
						<tr><th>Expected results</th></tr>
					</thead>
					<tbody>
						<tr><td><p><?php echo $course['expected_results']; ?></p></td></tr>
					</tbody>
				</table>
				<table>
					<thead>
						<tr>
							<th width="70%"><label>Synopsis Description:</label></th>
							<th><label>Horarium Lectures:</label></th>
							<th><label>Horarium Seminars:</label></th>
							<th><label>Horarium Practice:</label></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($synopsis as $item): ?>
						<tr>
							<td width="70%"><?php echo $item['description']; ?></td>
							<td><?php echo $item['horarium_lectures']; ?></td>
							<td><?php echo $item['horarium_seminars']; ?></td>
							<td><?php echo $item['horarium_practice']; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<table>
					<thead>
						<tr>
							<th>Exam Synopsis:</th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ($examSynopsis as $item): ?>
						<tr>
							<td><?php echo $item['description']; ?></td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
				<table>
					<thead>
						<tr><th>Bibliography:</th></tr>
					</thead>
					<tbody>
					<?php foreach ($bibliography as $entry): ?>
						<tr><td><?php echo "[".$entry['position']."] ".$entry['content']; ?></td></tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</section>
		<?php endforeach; ?>
	<?php else: ?>
		<p>No courses found.</p>
	<?php endif; ?>
</main>

<script>
	const links = document.querySelectorAll('.course a.delete-button');
	for (const link of links) {
		const courseId = link.dataset.courseid;
		link.onclick = async (ev) => {
			ev.preventDefault();
			const success = await fetch(`delete.php?courseid=${courseId}`, { method: 'DELETE' })
				.then((res) => {
					if (!res.ok) {
						throw Error(`Server returned status ${res.status}: ${res.statusText}`);
					}
					console.log(`Course with id ${courseId} deleted successfully.`);
					return true;
				}).catch((err) => {
					console.error(`Deleting course with id ${courseId} failed with the following error: ${err.message}`)
					return false;
				});
			if (success) {
				window.location.reload();
			}
		};
	}
</script>
</body>
</html>
