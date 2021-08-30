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
    <p>Playing around Three.js... I'm having fun !</p>
<?php include("_navigation.php"); ?>

</body>

<script type="module">

import * as THREE from './scripts/three.js/three.module.js';

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
    
    // SCENE
    const scene = new THREE.Scene();

    // BOX Geometry
    const boxWidth = 1;
    const boxHeight = 1;
    const boxDepth = 1;
    const boxGeometry = new THREE.BoxGeometry(boxWidth, boxHeight, boxDepth);
    // Box Material
    const boxMaterial = new THREE.MeshPhongMaterial({color: 0xffffff});
    const boxTexture = new THREE.TextureLoader().load("img/cubetexture.png");
    boxMaterial.map = boxTexture;
    // Box Mesh construction
    const rotationCube = new THREE.Mesh(boxGeometry, boxMaterial);
    // Box added to scene
    scene.add(rotationCube);
    
    // Same as Box but for lines
    const triPoints = [];
    triPoints.push( new THREE.Vector3( -2, 0, -1 ) );
    triPoints.push( new THREE.Vector3( 0, 2, -1 ) );
    triPoints.push( new THREE.Vector3( 2, 0, -1 ) );
    triPoints.push( new THREE.Vector3( 0, -2, -1 ) );
    triPoints.push( new THREE.Vector3( -2, 0, -1 ) );
    const triGeometry = new THREE.BufferGeometry().setFromPoints( triPoints );
    const triMaterial1 = new THREE.LineBasicMaterial({color: 0x008800});
    const triMaterial2 = new THREE.LineBasicMaterial({color: 0x009900});
    const triLines1 = new THREE.Line( triGeometry, triMaterial1 );
    const triLines2 = new THREE.Line( triGeometry, triMaterial2 );
    scene.add(triLines1);
    scene.add(triLines2);
    
    // LIGHT
    const color = 0xFFFFFF;
    const intensity = 1;
    const light = new THREE.DirectionalLight(color, intensity);
    light.position.set(-1, 2, 4);
    scene.add(light)
    
    
    const triLinesZ = -1;

    function render(time) {
        time *= 0.001;  // convert time to seconds

        rotationCube.rotation.y = time;
        rotationCube.rotation.x = Math.sin( time ) / 3;
        rotationCube.position.z = Math.sin( time * 2 ) + triLinesZ + 0.5;
        
        triLines1.rotation.z = -time * 120;
        triLines2.rotation.z = time * 120;
        triLines1.position.z = Math.cos( time * 2 ) + triLinesZ;
        triLines2.position.z = Math.cos( time * 2 ) + triLinesZ;

        renderer.render(scene, camera);

        requestAnimationFrame(render);
    }
    requestAnimationFrame(render);
}
startGL();
</script>

</html>