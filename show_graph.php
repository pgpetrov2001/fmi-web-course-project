<?php

require_once 'database.php';

$db = new Db();

function getCourseDependencies() {
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

$edges = getCourseDependencies();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course Entry</title>
	<script type="text/javascript" src="graph-visualization/graph.min.js"></script>
	<script>
		const edges = <?php echo json_encode($edges); ?>;
		const graph = {};
		for (const { parent_name, child_name } of edges) {
			graph[parent_name] ??= [];
			graph[child_name] ??= [];
			graph[parent_name].push(child_name);
		}

		console.log(graph);

		var drawing;
		function createDrawing() {
			drawing = new Drawing.SimpleGraph({
				layout: '3d',
				selection: true,
				graph,
				graphLayout: {
					attraction: 5,
					repulsion: 0.5
				},
				showStats: false,
				showInfo: true
			});
			drawing.show_labels = true;
		}
	</script>
	<script type="text/javascript" src="graph-visualization/specified_graph.js"></script>
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
    </style>
</head>

<body onload="createDrawing()">

<header>
	<h1>Course Graph</h1>
</header>

<main>
</main>

</body>
