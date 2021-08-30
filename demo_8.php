<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebGL Demo using "only" fragment Shader</title>
    <link rel="stylesheet" href="./styles/style.css" type="text/css">
</head>

<body>
    <h1>WebGL the fragment Shader</h1>
    <canvas id="glcanvas" width="640" height="480"></canvas>
    <p>Playing arround sinus, time and position.</p>
<?php include("_navigation.php"); ?>

</body>


<script type="module">
var gl;
var timeLocationR;
var timeLocationG;
var timeLocationB;
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

    shaderScript   = document.getElementById("2d-fragment-shader");
    shaderSource   = shaderScript.text;
    let fragmentShader = gl.createShader(gl.FRAGMENT_SHADER);
    gl.shaderSource(fragmentShader, shaderSource);
    gl.compileShader(fragmentShader);

    let program = gl.createProgram();
    gl.attachShader(program, vertexShader);
    gl.attachShader(program, fragmentShader);
    gl.linkProgram(program);	
    gl.useProgram(program);
    
    gl.clearColor(1.0, 0.0, 0.0, 1.0);
    gl.clear(gl.COLOR_BUFFER_BIT);
    
    let positionLocation = gl.getAttribLocation(program, "aVertexPosition");
    gl.enableVertexAttribArray(positionLocation);
    gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);
    
    timeLocationR = gl.getUniformLocation(program, "u_r_color");
    timeLocationG = gl.getUniformLocation(program, "u_g_color");
    timeLocationB = gl.getUniformLocation(program, "u_b_color");
    
    //gl.drawArrays(gl.TRIANGLES, 0, 6);
    window.requestAnimationFrame(render);

}

function render() {
    let ms = new Date().valueOf();
    gl.uniform1f(timeLocationR, 1.0 - Math.sin(ms/500.0));
    gl.uniform1f(timeLocationG, 1.0 - Math.sin(ms/400.0));
    gl.uniform1f(timeLocationB, 1.0 - Math.sin(ms/600.0));
    
    gl.drawArrays(gl.TRIANGLES, 0, 6);
    
    window.requestAnimationFrame(render);
}
</script>



<script id="2d-vertex-shader" type="x-shader/x-vertex">//
attribute vec2 aVertexPosition;

void main(void) {
    gl_Position = vec4(aVertexPosition, 0, 1);
}
// ]]></script>



<script id="2d-fragment-shader" type="x-shader/x-fragment">//
precision mediump float;

uniform float u_r_color;
uniform float u_g_color;
uniform float u_b_color;

void main(void) {
    float x,y;

    x = gl_FragCoord.x / 640.0;
    y = gl_FragCoord.y / 480.0;
    
    gl_FragColor = vec4(u_r_color*x, u_g_color*y, u_b_color*(1.5+x-y), 1.0);
}
// ]]></script>

</html>