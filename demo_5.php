<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebGL Demo using "only" fragment Shader</title>
    <link rel="stylesheet" href="./styles/style.css" type="text/css">
</head>

<body>
    <h1>WebGL Demo using "only" fragment Shader</h1>
    <canvas id="glcanvas" width="640" height="480"></canvas>
    <p>Mandelbrot exemple... looking smart ! (from the book).</p>
<?php include("_navigation.php"); ?>

</body>


<script type="module">

startGL();
//
// Start here
//
var canvas;
var gl;
var buffer;

var shaderScript;
var shaderSource;
var vertexShader;
var fragmentShader;

var program;
var positionLocation;

function startGL() {
    canvas = document.querySelector('#glcanvas');
    gl = canvas.getContext('webgl');

    // If we don't have a GL context, give up now

    if (!gl) {
      alert('Unable to initialize WebGL. Your browser or machine may not support it.');
      return;
    }
    gl.viewport(0, 0, gl.drawingBufferWidth, gl.drawingBufferHeight);
    
    
    buffer = gl.createBuffer();
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
    
    shaderScript = document.getElementById("2d-vertex-shader");
    shaderSource = shaderScript.text;
    vertexShader = gl.createShader(gl.VERTEX_SHADER);
    gl.shaderSource(vertexShader, shaderSource);
    gl.compileShader(vertexShader);

    shaderScript   = document.getElementById("2d-fragment-shader");
    shaderSource   = shaderScript.text;
    fragmentShader = gl.createShader(gl.FRAGMENT_SHADER);
    gl.shaderSource(fragmentShader, shaderSource);
    gl.compileShader(fragmentShader);

    program = gl.createProgram();
    gl.attachShader(program, vertexShader);
    gl.attachShader(program, fragmentShader);
    gl.linkProgram(program);	
    gl.useProgram(program);
    
    render();

  }

  function render() {

    window.requestAnimationFrame(render, canvas);

    gl.clearColor(1.0, 0.0, 0.0, 1.0);
    gl.clear(gl.COLOR_BUFFER_BIT);
    
    
    positionLocation = gl.getAttribLocation(program, "a_position");
    gl.enableVertexAttribArray(positionLocation);
    gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);
    
    gl.drawArrays(gl.TRIANGLES, 0, 6);

  }
</script>

<script id="2d-vertex-shader" type="x-shader/x-vertex">// 
attribute vec2 a_position;
  void main() {
    gl_Position = vec4(a_position, 0, 1);
  }
// ]]></script>

<script id="2d-fragment-shader" type="x-shader/x-fragment">// 
#define NUM_STEPS   50
#define ZOOM_FACTOR 2.0
#define X_OFFSET    0.5

#ifdef GL_FRAGMENT_PRECISION_HIGH
  precision highp float;
#else
  precision mediump float;
#endif
precision mediump int;

void main() {
  vec2 z;
  float x,y;
  int steps;
  float normalizedX = (gl_FragCoord.x - 320.0) / 640.0 * ZOOM_FACTOR *
                      (640.0 / 480.0) - X_OFFSET;
  float normalizedY = (gl_FragCoord.y - 240.0) / 480.0 * ZOOM_FACTOR;

  z.x = normalizedX;
  z.y = normalizedY;

  for (int i=0;i<NUM_STEPS;i++) {

      steps = i;

      x = (z.x * z.x - z.y * z.y) + normalizedX;
      y = (z.y * z.x + z.x * z.y) + normalizedY;

      if((x * x + y * y) > 4.0) {
                break;
              }

      z.x = x;
      z.y = y;

  }

  if (steps == NUM_STEPS-1) {
    gl_FragColor = vec4(0.0, 0.0, 0.6, 1.0);
  } else {
    gl_FragColor = vec4(0.0, 0.0, 0.0, 1.0);
  }
}
// ]]></script>

</body>
</html>