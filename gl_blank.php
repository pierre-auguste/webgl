<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebGL Demo</title>
    <link rel="stylesheet" href="./styles/style.css" type="text/css">
    <script src="./scripts/script.js" defer></script>
</head>

<body>
    <h1>WebGL blank</h1>
    <canvas id="glcanvas" width="640" height="480"></canvas>
    <p>Image generated with openGL.</p>
<?php include("_navigation.php"); ?>

</body>


<script type="module">
var gl;
startGL();
function startGL() {
    let canvas = document.querySelector('#glcanvas');
    gl = canvas.getContext('webgl');

    if (!gl) {
        alert('Unable to initialize WebGL. Your browser or machine may not support it.');
        return;
    }


    let buffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, buffer);
    gl.bufferData(
        gl.ARRAY_BUFFER, 
        new Float32Array([
            -1.0, -1.0, 
             1.0, -1.0, 
            -1.0,  1.0, 
            -1.0,  1.0, 
             1.0, -1.0, 
             1.0,  1.0]), 
        gl.STATIC_DRAW
        );

    let shaderScript = document.getElementById("2d-vertex-shader");
    let shaderSource = shaderScript.text;
    let vertexShader = gl.createShader(gl.VERTEX_SHADER);
    gl.shaderSource(vertexShader, shaderSource);
    gl.compileShader(vertexShader);
    if ( !gl.getShaderParameter( vertexShader, gl.COMPILE_STATUS) ) {
        var info = gl.getShaderInfoLog(vertexShader);
        throw 'Could not compile vertex shader. \n\n' + info;
    }

    shaderScript   = document.getElementById("2d-fragment-shader");
    shaderSource   = shaderScript.text;
    let fragmentShader = gl.createShader(gl.FRAGMENT_SHADER);
    gl.shaderSource(fragmentShader, shaderSource);
    gl.compileShader(fragmentShader);
    if ( !gl.getShaderParameter( fragmentShader, gl.COMPILE_STATUS) ) {
        var info = gl.getShaderInfoLog(fragmentShader);
        throw 'Could not compile fragment shader. \n\n' + info;
    }

    let program = gl.createProgram();
    gl.attachShader(program, vertexShader);
    gl.attachShader(program, fragmentShader);
    gl.linkProgram(program);
    if ( !gl.getProgramParameter( program, gl.LINK_STATUS) ) {
        var info = gl.getProgramInfoLog(program);
        throw 'Could not compile WebGL program. \n\n' + info;
    }
    gl.useProgram(program);
    
    gl.clearColor(1.0, 0.0, 0.0, 1.0);
    gl.clear(gl.COLOR_BUFFER_BIT);
    
    let positionLocation = gl.getAttribLocation(program, "aVertexPosition");
    gl.enableVertexAttribArray(positionLocation);
    gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);
    
    
    //gl.drawArrays(gl.TRIANGLES, 0, 6);
    window.requestAnimationFrame(render);

    function render() {    
        gl.drawArrays(gl.TRIANGLES, 0, 6);
        window.requestAnimationFrame(render);
    }
}
</script>



<script id="2d-vertex-shader" type="x-shader/x-vertex">//
attribute vec2 aVertexPosition;
varying highp vec2 v_textureCoord;

void main(void) {
    v_textureCoord = a_vertexPosition;
    gl_Position = vec4(aVertexPosition, 0, 1);
}
// ]]></script>



<script id="2d-fragment-shader" type="x-shader/x-fragment">//
precision mediump float;
varying highp vec2 v_textureCoord;

void main(void) {
    float x = (v_textureCoord.y + 1.0) / 2.0;
    gl_FragColor = vec4(0.0, 0.0, x, 1.0);
}
// ]]></script>

</html>