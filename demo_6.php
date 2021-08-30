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
    <p>Image generated with the fragment shader using position as color.</p>
<?php include("_navigation.php"); ?>

</body>


<script type="module">
startGL();
function startGL() {
    let canvas = document.querySelector('#glcanvas');
    let gl = canvas.getContext('webgl');

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
    
    let positionLocation = gl.getAttribLocation(program, "position");
    gl.enableVertexAttribArray(positionLocation);
    gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);

    gl.drawArrays(gl.TRIANGLES, 0, 6);
    //window.requestAnimationFrame(render, canvas);

}

function render() {
    window.requestAnimationFrame(render, canvas);
    gl.drawArrays(gl.TRIANGLES, 0, 6);
}
</script>



<script id="2d-vertex-shader" type="x-shader/x-vertex">// 
attribute vec2 position;
void main() {
    gl_Position = vec4(position, 0, 1);
}
// ]]></script>



<script id="2d-fragment-shader" type="x-shader/x-fragment">// 
#ifdef GL_FRAGMENT_PRECISION_HIGH
    precision highp float;
#else
    precision mediump float;
#endif
precision mediump int;

void main() {
    vec2 z;
    float x,y;

    x = gl_FragCoord.x / 640.0;
    y = gl_FragCoord.y / 240.0;

    z.x = x;
    z.y = y;

    gl_FragColor = vec4((z.x * z.y)/2.0, z.x, z.y, 0.5);
}
// ]]></script>

</html>