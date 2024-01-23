<?php

require_once 'database.php';

$db = new Db();

$minors = $db->getAll('minors');
$lecturers = $db->getAll('lecturers');
$courses = $db->getAll('courses');

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="static/create_course.css">
	<script type="text/javascript" src="static/xlsx.full.min.js"></script>
	<script type="text/javascript">
		const lecturers = <?php echo json_encode($lecturers); ?>;
	</script>
    <title>Add Course Entry</title>
</head>


<body>

<header>
	<h1>Create a Course</h1>
</header>

<main>

<label for="import-xlsx">Import course from Excel file:</label>
<input id="import-xlsx" type="file" accept="application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" onchange="importExcel()">

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
	<textarea id="prerequisites" name="prerequisites" rows="6"></textarea>

	<label for="expectedResults">Expected Results:</label>
	<textarea id="expectedResults" name="expectedResults" rows="6"></textarea>

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
<table id="course-content">
</table>
</main>

<script>
	function importExcel() {
		const input = document.querySelector('#import-xlsx');
		const file = input.files[0];
		const reader = new FileReader();
		reader.onload = (event) => {
			const data = event.target.result;
			const workbook = XLSX.read(data, { type: 'binary' });
			const sheetName = workbook.SheetNames[0];
			const sheet = workbook.Sheets[sheetName];
			parseSheet(sheet);
		};
		reader.readAsBinaryString(input.files[0]);
	}

	const fieldToCellMapping = {
		courseName: 'A17',
		credits: 'O30',
		lectureEngagement: 'O32',
		seminarEngagement: 'O33',
		practiceEngagement: 'O34',
		homeworkEngagement: 'O37',
		testPrepEngagement: 'O38',
		courseProjectEngagement: 'O39',
		selfStudyEngagement: 'O40',
		studyReportEngagement: 'O41',
		otherExtracurricularEngagement: 'O42',
		examPrepEngagement: 'O43',
		courseProjectGradePercentage: 'O55',
		testsGradePercentage: 'O52',
		examGradePercentage: 'O62',
		headnote: 'A65',
		prerequisites: 'A68',
	};

	function isUpperCase(s) {
		return s == s.toUpperCase();
	}
	function rtrim(str, ch) {
		let i = str.length - 1;
		while (ch === str.charAt(i) && i >= 0) i--;
		return str.substring(0, i + 1);
	}
	function isAcademicTitlePart(s) {
		s = rtrim(s.toLowerCase(), '.');
		if (['д-р', 'доц', 'гл', 'ас', 'инж', 'изсл', 'проф'].includes(s)) {
			return true;
		}
		return s.search('\\.') != -1;
	}

	function findMatchingStaffMember(names) {
		const tokens = names.trim().split(/\s+/);
		const nameTokens = tokens.filter((t) => t.length && !isAcademicTitlePart(t) && isUpperCase(t[0]));
		for (const nameToken of nameTokens) {
			if (nameToken.endsWith('.')) {
				if (nameToken.length != 2) {
					return null;
				}
			}
		}
		if (![2,3].includes(nameTokens.length)) {
			return null;
		}
		const match = (pattern, name) => {
			if (pattern.endsWith('.')) {
				return name[0] == pattern[0];
			}
			return name == pattern;
		};
		if (nameTokens.length == 2) {
			return lecturers.find((l) => {
				const names = l.names.trim().split(/\s+/);
				if (names.length != 3) {
					return false;
				}
				return match(nameTokens[0], names[0]) && match(nameTokens[1], names[2]);
			}) ?? null;
		}
		return lecturers.find((l) => {
			const names = l.names.trim().split(/\s+/);
			if (names.length != 3) {
				return false;
			}
			return match(nameTokens[0], names[0]) && match(nameTokens[1], names[1]) && match(nameTokens[2], names[2]);
		}) ?? null;
	}

	function parseSheet(sheet) {
		window.sheet = sheet;
		const result = {};
		for (const field in fieldToCellMapping) {
			const cellAddress = fieldToCellMapping[field];
			const value = sheet[cellAddress]?.v ?? 0;
			let htmlEl = document.querySelector(`input#${field}`);
			if (htmlEl) {
				if (field.endsWith('Percentage')) {
					htmlEl.value = parseInt(100*Number(value));
				} else {
					htmlEl.value = value;
				}
			} else {
				htmlEl = document.querySelector(`#${field}`);
				htmlEl.textContent = value;
			}
			result[field] = value;
		}
		const titular = sheet['U10']?.v ?? null;
		let lecturer = null;
		if (titular) {
			lecturer = findMatchingStaffMember(titular);
		}
		if (lecturer) {
			const htmlEl = document.querySelector(`#courseForm select#lecturer option[value="${lecturer.id}"]`);
			console.log(htmlEl);
			htmlEl.setAttribute('selected', '');
			console.log(htmlEl);
		}
		result.lecturer = lecturer;
		return result;
	}

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