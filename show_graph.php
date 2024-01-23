<?php

require_once 'database.php';

$db = new Db();

$edges = $db->getCourseDependencies();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="static/show_graph.css">
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
</head>

<body onload="createDrawing()">

<header>
	<h1>Course Graph</h1>
</header>

<main>
</main>

</body>
