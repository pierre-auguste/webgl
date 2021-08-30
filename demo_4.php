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
    <p>This statue is imported from Blender to webGL using Three.js library. Use your mouse to move it !</p>
<?php include("_navigation.php"); ?>

</body>

<script type="module">

import * as THREE from './scripts/three.js/three.module.js';
import { OrbitControls } from './scripts/three.js/OrbitControls.js';
import { GLTFLoader } from './scripts/three.js/GLTFLoader.js';

function startGL() {
    const canvas = document.querySelector('#glcanvas');
    const renderer = new THREE.WebGLRenderer({canvas});

    // CAMERA
    const fov = 56;
    const aspect = 4/3;  // the canvas default
    const near = 0.1;
    const far = 50;
    const camera = new THREE.PerspectiveCamera(fov, aspect, near, far);
    camera.position.set ( 0, 0, 2 );
    camera.lookAt( 0, 0, 0 );
    
    // CONTROLS
    const controls = new OrbitControls( camera, renderer.domElement );

    // SCENE
    const scene = new THREE.Scene();

    // LIGHT
    const color = 0xFFFFFF;
    const intensity = 5;
    const light1 = new THREE.DirectionalLight(color, intensity);
    const light2 = new THREE.DirectionalLight(color, intensity - 4);
    light1.position.set(-1, 2, 4);
    light2.position.set(0, 1, -2);
    scene.add(light1);
    scene.add(light2);
    
    // LODING BLENDER STATUE
    const loader = new GLTFLoader();
    loader.load( 'meshes/statue.glb', function ( gltf ) {
            scene.add( gltf.scene );
    }, undefined, function ( error ) {
            console.error( error );
    } );
    
    // RENDERING
    function render(time) {
        time *= 0.001;  // convert time to seconds

        renderer.render(scene, camera);

        requestAnimationFrame(render);
    }
    requestAnimationFrame(render);
}
startGL();
</script>

</html>