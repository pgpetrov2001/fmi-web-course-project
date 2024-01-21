<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course Entry</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
		h1 {
			text-align: center;
		}
        form {
            max-width: 800px;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
		table {
			width: 100%;
		}
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select, textarea {
            width: calc(100% - 16px);
            padding: 8px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
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
		button:disabled {
			background-color: gray;
		}
		#minorsContainer{
			display: flex;
			flex-wrap: wrap;
			align-content: flex-start;
			justify-content: space-evenly;
			input[type=checkbox] {
				width: 15px;
				height: 15px;
			}
		}
    </style>
</head>

<?php

require_once 'database.php';

$db = new Db();

function getAll($object) {
	global $db;
	$sql = "SELECT * FROM ".$object;
	$stmt = $db->getConnection()->query($sql);
	$items = $stmt->fetchAll();
	return $items;
}

$minors = getAll('minors');
$lecturers = getAll('lecturers');
$courses = getAll('courses');

?>

<body>

<header>
	<h1>Create a Course</h1>
</header>

<main>
<form id="courseForm" action="process_form.php" method="post">
	<label for="courseName">Course Name:</label>
	<input type="text" id="courseName" name="courseName" required>

	<fieldset id="minorsContainer">
		<legend>Select relevant minors</legend>
		<?php foreach ($minors as $minor): ?>
		<?php $optionId = "courseMinors-".$minor['abbreviation'] ?>
		<div>
			<label for="<?php echo $optionId; ?>">
				<?php echo strtoupper($minor['abbreviation']); ?>
			</label>
			<input type="checkbox" id="<?php echo $optionId; ?>" name="courseMinors[]" value="<?php echo $minor['id']; ?>">
		</div>
		<?php endforeach; ?>
	</fieldset>

	<label for="lecturer">Lecturer:</label>
	<select id="lecturer" name="lecturer" required>
	<?php foreach ($lecturers as $lecturer): ?>
		<option value="<?php echo $lecturer['id']; ?>"> <?php echo $lecturer['titles'].' '.$lecturer['names']; ?> </option>
	<?php endforeach; ?>
	</select>

	<fieldset>
		<legend>Select courses that this course depends on</legend>
		<div id="parentCoursesContainer">
			<label>Courses:</label>
		</div>
	</fieldset>
	<button type="button" onclick="addParentCourseEntry()">Add Parent Course</button>

	<label for="headnote">Headnote:</label>
	<textarea id="headnote" name="headnote" rows="6" required></textarea>

	<label for="prerequisites">Prerequisites:</label>
	<textarea id="prerequisites" name="prerequisites" rows="6" required></textarea>

	<label for="expectedResults">Expected Results:</label>
	<textarea id="expectedResults" name="expectedResults" rows="6" required></textarea>

	<label for="credits">Credits:</label>
	<input type="number" id="credits" name="credits" required min="0" value="0">

	<fieldset id="synopsisContainer">
		<label>Synopsis:</label>
		<table>
			<thead>
				<tr>
					<th><label>Horarium Lectures:</label></th>
					<th><label>Horarium Seminars:</label></th>
					<th><label>Horarium Practice:</label></th>
					<th width="70%"><label>Synopsis Description:</label></th>
				</tr>
			</thead>
			<tbody>
				<tr class="synopsis-item">
					<input type="hidden" class="synopsisPosition" name="synopsisPosition[]" value="1" min="1" required>
					<td> <input type="number" class="synopsisHorariumLectures" name="synopsisHorariumLectures[]" value="0" min="0" placeholder="Horarium Lectures (hours)" required> </td>
					<td> <input type="number" class="synopsisHorariumSeminars" name="synopsisHorariumSeminars[]" value="0" min="0" placeholder="Horarium Seminars (hours)" required> </td>
					<td> <input type="number" class="synopsisHorariumPractice" name="synopsisHorariumPractice[]" value="0" min="0" placeholder="Horarium Practice (hours)" required> </td>
					<td width="70%"> <textarea class="synopsisDescription" name="synopsisDescription[]" rows="4" placeholder="Synopsis Description"></textarea> </td>
				</tr>
			</tbody>
		</table>
	</fieldset>
	<button type="button" onclick="addSynopsisEntry()">Add Synopsis Item</button>

	<fieldset id="examSynopsisContainer">
		<label>Exam Synopsis:</label>
		<div class="exam-synopsis-item">
			<input type="hidden" class="examSynopsisPosition" name="examSynopsisPosition[]" value="1" min="1" required>
			<textarea class="examSynopsisDescription" name="examSynopsisDescription[]" rows="4" placeholder="Entry Description"></textarea>
		</div>
	</fieldset>
	<button type="button" onclick="addExamSynopsisEntry()">Add Exam Synopsis Entry</button>

	<div style="display: flex; justify-content: space-between; margin-top: 20px;">
		<div style="width: 32%;">
			<label for="lectureEngagement">Lecture Engagement:</label>
			<input type="number" id="lectureEngagement" name="lectureEngagement" required min="0" value="0">

			<label for="seminarEngagement">Seminar Engagement:</label>
			<input type="number" id="seminarEngagement" name="seminarEngagement" required min="0" value="0">

			<label for="practiceEngagement">Practice Engagement:</label>
			<input type="number" id="practiceEngagement" name="practiceEngagement" required min="0" value="0">

			<label for="selfStudyEngagement">Self Study Engagement:</label>
			<input type="number" id="selfStudyEngagement" name="selfStudyEngagement" required min="0" value="0">

			<label for="otherExtracurricularEngagement">Other Extracurricular Engagement:</label>
			<input type="number" id="otherExtracurricularEngagement" name="otherExtracurricularEngagement" required min="0" value="0">
		</div>
		<div style="width: 32%;">
			<label for="homeworkEngagement">Homework Engagement:</label>
			<input type="number" id="homeworkEngagement" name="homeworkEngagement" required min="0" value="0">

			<label for="testPrepEngagement">Test Prep Engagement:</label>
			<input type="number" id="testPrepEngagement" name="testPrepEngagement" required min="0" value="0">

			<label for="courseProjectEngagement">Course Project Engagement:</label>
			<input type="number" id="courseProjectEngagement" name="courseProjectEngagement" required min="0" value="0">

			<label for="studyReportEngagement">Study Report Engagement:</label>
			<input type="number" id="studyReportEngagement" name="studyReportEngagement" required min="0" value="0">

			<label for="examPrepEngagement">Exam Prep Engagement:</label>
			<input type="number" id="examPrepEngagement" name="examPrepEngagement" required min="0" value="0">
		</div>
		<div style="width: 32%;">
			<label for="courseProjectGradePercentage">Course Project Grade Percentage:</label>
			<input type="number" id="courseProjectGradePercentage" name="courseProjectGradePercentage" step="1" required min="0" max="100" value="0">

			<label for="testsGradePercentage">Tests Grade Percentage:</label>
			<input type="number" id="testsGradePercentage" name="testsGradePercentage" step="1" required min="0" max="100" value="0">

			<label for="examGradePercentage">Exam Grade Percentage:</label>
			<input type="number" id="examGradePercentage" name="examGradePercentage" step="1" required min="0" max="100" value="0">
		</div>
	</div>

	<div id="bibliographiesContainer">
		<label>Bibliographies:</label>
		<div class="bibliography-item">
			<input type="hidden" class="bibliographyPosition" name="bibliographyPosition[]" value="1" min="1" required>
			<textarea class="bibliographyContent" name="bibliographyContent[]" rows="2" placeholder="[1]"></textarea>
		</div>
	</div>
	<button type="button" onclick="addBibliography()">Add Bibliography</button>

	<button type="submit">Add Course</button>
</form>
</main>

<script>
    function addSynopsisEntry() {
        const synopsisContainer = document.querySelector('#synopsisContainer table tbody');
		const position = synopsisContainer.querySelectorAll('.synopsis-item').length + 1;
        const newSynopsisDiv = document.createElement('tr');
		newSynopsisDiv.classList.add('synopsis-item');
        newSynopsisDiv.innerHTML = `
            <input type="hidden" class="synopsisPosition" name="synopsisPosition[]" value="${position}" min="1" required>
			<td> <input type="number" class="synopsisHorariumLectures" name="synopsisHorariumLectures[]" value="0" min="0" placeholder="Horarium Lectures (hours)" required> </td>
			<td> <input type="number" class="synopsisHorariumSeminars" name="synopsisHorariumSeminars[]" value="0" min="0" placeholder="Horarium Seminars (hours)" required> </td>
			<td> <input type="number" class="synopsisHorariumPractice" name="synopsisHorariumPractice[]" value="0" min="0" placeholder="Horarium Practice (hours)" required> </td>
			<td width="70%"> <textarea class="synopsisDescription" name="synopsisDescription[]" rows="4" placeholder="Synopsis Description"></textarea> </td>
        `;
        synopsisContainer.appendChild(newSynopsisDiv);
    }

    function addExamSynopsisEntry() {
        const synopsisContainer = document.getElementById('examSynopsisContainer');
		const position = synopsisContainer.querySelectorAll('.exam-synopsis-item').length + 1;
        const newSynopsisDiv = document.createElement('div');
		newSynopsisDiv.classList.add('exam-synopsis-item');
        newSynopsisDiv.innerHTML = `
            <input type="hidden" class="examSynopsisPosition" name="examSynopsisPosition[]" value="${position}" min="1" required>
			<textarea class="examSynopsisDescription" name="examSynopsisDescription[]" rows="4" placeholder="Entry Description"></textarea>
        `;
        synopsisContainer.appendChild(newSynopsisDiv);
    }

	function addParentCourseEntry() {
        const parentCoursesContainer = document.getElementById('parentCoursesContainer');
		const position = parentCoursesContainer .querySelectorAll('.parent-course').length + 1;
        const newParentCourseDiv = document.createElement('div');
		newParentCourseDiv.classList.add('parent-course');
        newParentCourseDiv.innerHTML = `
			<select name="parentCourses[]">
				<option value="null" selected>N/A</option>
			<?php foreach ($courses as $course): ?>
				<option value="<?php echo $course['id']; ?>"> <?php echo $course['name']; ?> </option>
			<?php endforeach; ?>
			</select>
        `;
        parentCoursesContainer.appendChild(newParentCourseDiv);
	}

    function addBibliography() {
        const bibliographiesContainer = document.getElementById('bibliographiesContainer');
		const position = bibliographiesContainer.querySelectorAll('.bibliography-item').length + 1;
        const newBibliographyDiv = document.createElement('div');
		newBibliographyDiv.classList.add('bibliography-item');
        newBibliographyDiv.innerHTML = `
            <input type="hidden" class="bibliographyPosition" name="bibliographyPosition[]" value="${position}" min="1" required>
            <textarea class="bibliographyContent" name="bibliographyContent[]" rows="2" placeholder="[${position}]"></textarea>
        `;
        bibliographiesContainer.appendChild(newBibliographyDiv);
    }

	const fields = [
		document.querySelector("#courseProjectGradePercentage"),
		document.querySelector("#testsGradePercentage"),
		document.querySelector("#examGradePercentage"),
	];
	const trackConstraint = () => {
		let sum = 0;
		for (const field of fields) {
			sum += parseInt(field.value, 10) || 0;
		}
		document.querySelector('form [type=submit]').disabled = sum !== 100;
	};
	for (const field of fields) {
		field.oninput = trackConstraint;
	}
	trackConstraint();
</script>

</body>
</html>
