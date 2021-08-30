<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebGL Demo with three.js</title>
    <link rel="stylesheet" href="./styles/style.css" type="text/css">
</head>
<body>
    <h1>WebGL Demo with three.js</h1>
    <canvas id="glcanvas" width="640" height="480"></canvas>
    <p>The same cube using Three.js framework which is handling the render part. (From the book)</p>
<?php include("_navigation.php"); ?>

</body>

<script type="module">

import * as THREE from './scripts/three.js/three.module.js';

function startGL() {
    const canvas = document.querySelector('#glcanvas');
    const renderer = new THREE.WebGLRenderer({canvas});
    
    // Camera
    const fov = 56;
    const aspect = 4/3;  // the canvas default
    const near = 0.1;
    const far = 5;
    const camera = new THREE.PerspectiveCamera(fov, aspect, near, far);
    camera.position.z = 2;
    
    // Scene
    const scene = new THREE.Scene();

    // Box Geometry
    const boxWidth = 1;
    const boxHeight = 1;
    const boxDepth = 1;
    const geometry = new THREE.BoxGeometry(boxWidth, boxHeight, boxDepth);
    
    // Box Material
    const material = new THREE.MeshPhongMaterial({color: 0xffffff});
    const texture = new THREE.TextureLoader().load("img/cubetexture.png");
    material.map = texture;
    
    // Box Mesh construction
    const cube = new THREE.Mesh(geometry, material);
    
    // Box added to scene
    scene.add(cube);
    
    // Light
    const color = 0xFFFFFF;
    const intensity = 1;
    const light = new THREE.DirectionalLight(color, intensity);
    light.position.set(-1, 2, 4);
    scene.add(light);
    

    function render(time) {
        time *= 0.001;  // convert time to seconds

        cube.rotation.x = time;
        cube.rotation.y = time;

        renderer.render(scene, camera);

        requestAnimationFrame(render);
    }
    requestAnimationFrame(render);
}
startGL();
</script>

</html>