<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebGL Demo using "only" fragment Shader</title>
    <link rel="stylesheet" href="./styles/style.css" type="text/css">
</head>

<body>
    <h1>WebGL the fragment Shader</h1>
    <canvas id="glcanvas" width="640px" height="480px"></canvas>
    <p>Two textures is better than one. Use your mouse.</p>
<?php include("_navigation.php"); ?>

</body>


<script type="module">

var mousePos = new Float32Array(2);

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
            // First triangle:
             1.0,  1.0,
            -1.0,  1.0,
            -1.0, -1.0,
            // Second triangle:
            -1.0, -1.0,
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
    
    let texturesLoc = [];
    texturesLoc[0] = gl.getUniformLocation(program, "u_cube1Texture");
    gl.uniform1i(texturesLoc[0], 0); // 0
    texturesLoc[1] = gl.getUniformLocation(program, "u_cube2Texture");
    gl.uniform1i(texturesLoc[1], 1); // 1
    
    function setImage(img, loc) {
        let texture = gl.createTexture();
        switch (loc) {
            case 1:
                gl.activeTexture(gl.TEXTURE1); // 1
                break;
            default:
                gl.activeTexture(gl.TEXTURE0); // 0
                break;
        }
        //gl.activeTexture(gl.TEXTURE0); // 0
        gl.bindTexture(gl.TEXTURE_2D, texture);
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGB, gl.RGB, gl.UNSIGNED_BYTE, img);
        
        if (isPowerOf2(img.width) && isPowerOf2(img.height)) {
            gl.generateMipmap(gl.TEXTURE_2D);
        } else {
            gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.LINEAR);
            gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.CLAMP_TO_EDGE);
            gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.CLAMP_TO_EDGE);
        }
    }
    
    const cube1Img = new Image();
    cube1Img.onload = function() {
        setImage(cube1Img, 0);
    };
    cube1Img.src = 'img/cubetexture.png';
    
    const cube2Img = new Image();
    cube2Img.onload = function() {
        setImage(cube2Img, 1);
    };
    cube2Img.src = 'img/at_desk.png';
    
    let positionLocation = gl.getAttribLocation(program, "a_vertexPosition");
    gl.enableVertexAttribArray(positionLocation);
    gl.vertexAttribPointer(positionLocation, 2, gl.FLOAT, false, 0, 0);
    
    let circleLocation = gl.getUniformLocation(program, "u_circlePosition");
    
    let frame = 0;
    let x, y;
    
    function render() {
        
        document.getElementsByTagName("body")[0].onmousemove = findMouseCoords;
        if (mousePos[0] > 0 && mousePos[1] > 0)
        {
            x = mousePos[0] / (canvas.clientWidth/2) - 1.0;
            y = 1.0 - mousePos[1] / (canvas.clientHeight/2);
        }
        else
        {
            frame = frame + 1;
            x = Math.sin(frame/200) * 0.3;
            y = 0.15;
        }
        gl.uniform2f(circleLocation, x, y);
        
        window.requestAnimationFrame(render);
        gl.drawArrays(gl.TRIANGLES, 0, 6);
    }
    //gl.drawArrays(gl.TRIANGLES, 0, 6);
    window.requestAnimationFrame(render);    

    // Cleanup:
    gl.bindBuffer(gl.ARRAY_BUFFER, null);

}
function findMouseCoords(mouseEvent)
{
    let obj = document.getElementById("glcanvas");
    let obj_left = 0;
    let obj_top = 0;
    while (obj.offsetParent)
    {
        obj_left += obj.offsetLeft;
        obj_top += obj.offsetTop;
        obj = obj.offsetParent;
    }
    if (mouseEvent.pageX <= obj_left || mouseEvent.pageX >= obj_left+640
        || mouseEvent.pagey <= obj_top || mouseEvent.pageY >= obj_top+480)
    {
        mousePos[0] = 0;
        mousePos[1] = 0;
    }
    else
    {
        mousePos[0] = mouseEvent.pageX - obj_left;
        mousePos[1] = mouseEvent.pageY - obj_top;
    }
}
function isPowerOf2(value) {
  return (value & (value - 1)) === 0;
}
</script>



<script id="2d-vertex-shader" type="x-shader/x-vertex">// 
attribute vec2 a_vertexPosition;
varying highp vec2 v_textureCoord;
varying highp vec2 v_scale1;
varying highp vec2 v_scale2;

void main(void) {
    v_scale1 = vec2(0.5, -0.5);
    v_scale2 = vec2((640.0/1920.0)/0.666, (480.0/1080.0)/0.89);
    v_textureCoord = a_vertexPosition;
    gl_Position = vec4(a_vertexPosition, 0.0, 1.0);
}
// ]]></script>



<script id="2d-fragment-shader" type="x-shader/x-fragment">//
precision mediump float;

uniform sampler2D u_cube1Texture;
uniform sampler2D u_cube2Texture;
uniform vec2 u_circlePosition;

varying highp vec2 v_textureCoord;
varying highp vec2 v_scale1;
varying highp vec2 v_scale2;

float circle_radius = 0.3;
float fading = 0.4;

void main(void) {
    vec2 uv = v_textureCoord.xy;
    uv -= u_circlePosition;
    // simple
    float dist =  sqrt(dot(uv, uv));
    
    if (dist > circle_radius)
    {
        if (dist > circle_radius+fading)
        {
            gl_FragColor = texture2D(u_cube1Texture, v_textureCoord * v_scale1 + v_scale1);
        }
        else
        {
            float alpha = (1.0 / (fading / (dist - circle_radius)));
            gl_FragColor = (texture2D(u_cube1Texture, v_textureCoord * v_scale1 + v_scale1) * alpha)
                         + (texture2D(u_cube2Texture, -v_textureCoord * v_scale2 + v_scale2) * (1.0 - alpha));
        }
    }
    else
    { 
        gl_FragColor = texture2D(u_cube2Texture, -v_textureCoord * v_scale2 + v_scale2);
    }
}
// ]]></script>

</html>