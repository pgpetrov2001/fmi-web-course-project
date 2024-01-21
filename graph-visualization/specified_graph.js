var Drawing = Drawing || {};

Drawing.SimpleGraph = function(options) {
	options = options || {};

	this.layout = options.layout || "2d";
	this.layout_options = options.graphLayout || {};
	this.show_info = options.showInfo || false;
	this.show_labels = options.showLabels || false;
	this.selection = options.selection || false;
	this.limit = options.limit || 10;
	this.raw_graph = options.graph || {};

	let camera, controls, scene, renderer, interaction, geometry, object_selection;
	const info_text = {};
	const graph = new GRAPHVIS.Graph({limit: options.limit});

	const geometries = [];
	window.geometries = geometries;
	const arrows = [];

	const that=this;

	init();
	parseRawGraph();
	animate();

	function init() {
		// Three.js initialization
		renderer = new THREE.WebGLRenderer({alpha: true, antialias: true});
		renderer.setPixelRatio(window.devicePixelRatio);
		renderer.setSize(window.innerWidth, window.innerHeight);


		camera = new THREE.PerspectiveCamera(40, window.innerWidth/window.innerHeight, 1, 1000000);
		camera.position.z = 10000;

		controls = new THREE.TrackballControls(camera);

		controls.rotateSpeed = 0.5;
		controls.zoomSpeed = 5.2;
		controls.panSpeed = 1;

		controls.noZoom = false;
		controls.noPan = false;

		controls.staticMoving = false;
		controls.dynamicDampingFactor = 0.3;

		controls.keys = [ 65, 83, 68 ];

		controls.addEventListener('change', render);

		scene = new THREE.Scene();

		// Node geometry
		if(that.layout === "3d") {
			geometry = new THREE.SphereGeometry(30);
		} else {
			geometry = new THREE.BoxGeometry( 50, 50, 0 );
		}

		// Create node selection, if set
		if(that.selection) {
			object_selection = new THREE.ObjectSelection({
				domElement: renderer.domElement,
				selected: function(obj) {
					// display info
					if(obj !== null) {
						info_text.select = "Object " + obj.id;
					} else {
						delete info_text.select;
					}
				},
				clicked: function(obj) {
				}
			});
		}

		document.body.appendChild( renderer.domElement );

		// Create info box
		if(that.show_info) {
			const info = document.createElement("div");
			let id_attr = document.createAttribute("id");
			id_attr.nodeValue = "graph-info";
			info.setAttributeNode(id_attr);
			document.body.appendChild( info );
		}
	}


	/**
	 *  Creates a graph with random nodes and edges.
	 *  Number of nodes and edges can be set with
	 *  numNodes and numEdges.
	 */
	function parseRawGraph() {
		const nodes = [];
		const graphNodeFromRawNode = {};

		let id = 0;
		for (const raw_node in that.raw_graph) {
			const node = new GRAPHVIS.Node(id);
			node.data.title = raw_node;
			nodes.push(node);
			graphNodeFromRawNode[raw_node] = node;
			id++;
		}

		for (const node of nodes) {
			graph.addNode(node);
			drawNode(node);
		}

		for (const raw_node in that.raw_graph) {
			const node = graphNodeFromRawNode[raw_node];
			const neighbors = that.raw_graph[raw_node];
			for (const raw_neighbor of neighbors) {
				const neighbor = graphNodeFromRawNode[raw_neighbor];
				graph.addEdge(node, neighbor);
				drawEdge(node, neighbor);
			}
		}

		that.layout_options.width = that.layout_options.width || 2000;
		that.layout_options.height = that.layout_options.height || 2000;
		that.layout_options.iterations = that.layout_options.iterations || 100000;
		that.layout_options.layout = that.layout_options.layout || that.layout;
		graph.layout = new Layout.ForceDirected(graph, that.layout_options);
		graph.layout.init();
		info_text.nodes = "Nodes " + graph.nodes.length;
		info_text.edges = "Edges " + graph.edges.length;
	}


	/**
	 *  Create a node object and add it to the scene.
	 */
	function drawNode(node) {
		const draw_object = new THREE.Mesh( geometry, new THREE.MeshBasicMaterial( {  color: Math.random() * 0xe0e0e0, opacity: 0.8 } ) );
		let label_object;

		if (that.show_labels) {
			if (node.data.title !== undefined) {
				label_object = new THREE.Label(node.data.title);
			} else {
				label_object = new THREE.Label(node.id);
			}
			node.data.label_object = label_object;
			scene.add( node.data.label_object );
		}

		const area = 5000;
		draw_object.position.x = Math.floor(Math.random() * (area + area + 1) - area);
		draw_object.position.y = Math.floor(Math.random() * (area + area + 1) - area);

		if(that.layout === "3d") {
			draw_object.position.z = Math.floor(Math.random() * (area + area + 1) - area);
		}

		draw_object.id = node.id;
		node.data.draw_object = draw_object;
		node.position = draw_object.position;
		scene.add( node.data.draw_object );
	}


	/**
	 *  Create an edge object (line) and add it to the scene.
	 */
	function drawEdge(source, target) {
		material = new THREE.LineBasicMaterial({ color: 0x606060 });

		const sourcePos = source.data.draw_object.position;
		const targetPos = target.data.draw_object.position;

		const tmp_geo = new THREE.Geometry();
		tmp_geo.vertices.push(sourcePos);
		tmp_geo.vertices.push(targetPos);

		const direction = new THREE.Vector3().subVectors(targetPos, sourcePos);

		const arrow = new THREE.ArrowHelper(direction.clone().normalize(), sourcePos, direction.length(), 0x606060, 0.05 * direction.length );
		arrow.line.frustumCulled = false;

		arrows.push(arrow);
		geometries.push(tmp_geo);

		//scene.add( line );
		scene.add(arrow);
	}


	function animate() {
		requestAnimationFrame( animate );
		controls.update();
		render();
		if(that.show_info) {
			printInfo();
		}
	}


	function render() {
		let length, node;

		// Generate layout if not finished
		if(!graph.layout.finished) {
			info_text.calc = "<span style='color: red'>Calculating layout...</span>";
			graph.layout.generate();
		} else {
			info_text.calc = "";
		}

		// Update position of lines (edges)
		for (let i=0; i<geometries.length; i++) {
			geometries[i].verticesNeedUpdate = true;
		}

		for (let i=0; i<arrows.length; i++) {
			const [ v0, v1 ] = geometries[i].vertices;
			arrows[i].position.set(v0.x, v0.y, v0.z);
			const direction = new THREE.Vector3().subVectors(v1, v0);
			arrows[i].setDirection(direction.clone().normalize());
			arrows[i].setLength(direction.length(), direction.length()*0.1, direction.length()*0.05);
		}


		// Show labels if set
		// It creates the labels when this options is set during visualization
		if(that.show_labels) {
			length = graph.nodes.length;
			for (let i=0; i<length; i++) {
				node = graph.nodes[i];
				if (node.data.label_object !== undefined) {
					node.data.label_object.position.x = node.data.draw_object.position.x;
					node.data.label_object.position.y = node.data.draw_object.position.y - 100;
					node.data.label_object.position.z = node.data.draw_object.position.z;
					node.data.label_object.lookAt(camera.position);
				} else {
					let label_object;
					if (node.data.title !== undefined) {
						label_object = new THREE.Label(node.data.title, node.data.draw_object);
					} else {
						label_object = new THREE.Label(node.id, node.data.draw_object);
					}
					node.data.label_object = label_object;
					scene.add( node.data.label_object );
				}
			}
		} else {
			length = graph.nodes.length;
			for (let i=0; i<length; i++) {
				node = graph.nodes[i];
				if(node.data.label_object !== undefined) {
					scene.remove( node.data.label_object );
					node.data.label_object = undefined;
				}
			}
		}

		if (that.selection) {
			object_selection.render(scene, camera);
		}

		renderer.render( scene, camera );
	}

	/**
	 *  Prints info from the attribute info_text.
	 */
	function printInfo(text) {
		let str = '';
		for (const index in info_text) {
			if(str !== '' && info_text[index] !== '') {
				str += " - ";
			}
			str += info_text[index];
		}
		document.getElementById("graph-info").innerHTML = str;
	}
};
