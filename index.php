<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="static/fontawesome/css/all.min.css">
	<link rel="stylesheet" href="static/index.css">
    <title>Display Courses</title>
</head>

<?php

require_once 'database.php';

$db = new Db();

// Fetch courses from the database
try {
	$courses = $db->getCourses();
	$synopsisFromCourseId = $db->getSynopses();
	$examSynopsisFromCourseId = $db->getExamSynopses();
	$bibliographyFromCourseId = $db->getBibliographies();
	$minorsFromCourseId = $db->getMinors();
	$parentsFromCourseId = $db->getCoursesParents();
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

<aside>
	<nav>
		<ul>
			<li><a onclick="window.scrollTo(0,0)">Home</a>
			<br/>
			<?php foreach ($courses as $course): ?>
			<li><a href="#course-<?php echo $course['id']; ?>"><?php echo $course['name']; ?></a></li>
			<?php endforeach; ?>
		</ul>
	</nav>
</aside>

<main>
	<?php if (!empty($courses)): ?>
		<?php foreach ($courses as $course): ?>
			<?php $synopsis = $synopsisFromCourseId[$course['id']] ?? array(); ?>
			<?php $examSynopsis = $examSynopsisFromCourseId[$course['id']] ?? array(); ?>
			<?php $bibliography = $bibliographyFromCourseId[$course['id']] ?? array(); ?>
			<?php $minors = $minorsFromCourseId[$course['id']] ?? array(); ?>
			<section class="course" id="course-<?php echo $course['id']; ?>">
				<div class="curriculumContainer">
					<a href="#" class="export-button" data-courseid="<?php echo $course['id']; ?>">
						<button><i class="fa-solid fa-file-export"></i> Export JSON </button>
					</a>
					<a href="#" class="delete-button" data-courseid="<?php echo $course['id']; ?>">
						<button><i class="fa fa-trash"></i> Delete </button>
					</a>
				</div>
				<h2>Curriculum</h2>
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
	const deleteLinks = document.querySelectorAll('.course a.delete-button');
	for (const link of deleteLinks) {
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

	const exportLinks = document.querySelectorAll('.course a.export-button');
	for (const link of exportLinks) {
		const courseId = link.dataset.courseid;
		link.onclick = async (ev) => {
			ev.preventDefault();
			const data = await fetch(`export.php?courseid=${courseId}`, { method: 'GET' })
				.then((res) => {
					if (!res.ok) {
						throw Error(`Server returned status ${res.status}: ${res.statusText}`);
					}
					console.log(`Course with id ${courseId} JSON fetched successfully.`);
					return res.json();
				}).catch((err) => {
					console.error(`Fetching course with id ${courseId} failed with the following error: ${err.message}`)
					return false;
				});
			
			const fakeLink = document.createElement('a');
			fakeLink.download = `ИД-${data['Дисциплина']}.json`;
			const blob = new Blob([JSON.stringify(data)], { type: 'text/plain' })
			fakeLink.href = URL.createObjectURL(blob);
			document.body.appendChild(fakeLink);
			fakeLink.click();
			document.body.removeChild(fakeLink);
		};
	}

</script>
</body>
</html>
