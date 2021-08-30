<?php
$rows = (isset($_GET['rows']) AND $_GET['rows'] >= 6 AND $_GET['rows'] <= 30) ? (int)$_GET['rows'] : 12;
?><html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebGL Archerx experiments, what ?</title>
    <link rel="stylesheet" href="./styles/style.css" type="text/css">
    <style>
        #ktable {
            width: 640px;
        }
        #ktable th, #ktable td {
            width: 24%;
        }
    </style>
</head>

<body>
    <h1>Archerx experiments, what ?</h1>
    
    <canvas onclick="glp.updateKernels();" id="glcanvas" width="640" height="640"></canvas>
    
    <form>
        Rows : <input type="number" id="rows" name="rows" min="6" max="30" value="<?php echo $rows; ?>">&nbsp;&nbsp;&nbsp;&nbsp;
        <button onclick="window.location.reload();">Reload</button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button onclick="glp.updateKernels(); return false;">Update kernels</button>&nbsp;&nbsp;&nbsp;&nbsp;
    </form>
    
    
    <table id="ktable">
        <caption>Kernel infos :</caption>
        <tr>
            <th>Action :</th><td id="progress">...</td>
            <th>Rows :</th><td id="rows_info">0</td>
        </tr>
        <tr>
            <th>Frame :</th><td id="frame">0</td>
            <th>Octaves :</th><td id="octaves"></td>
        </tr>
        <tr>
            <th>Color average :</th><td id="colorsum">0</td>
            <th>Dilation : </th><td id="dilation">0</td></tr>
    </table>
    <table>
        <tr><th>GL version :</th><td id="glversion">0</td></tr>
        <tr><th>GLSL version :</th><td id="glslversion">0</td></tr>
    </table>
    <p>Let's use randomness ! (Guru: <a href="https://machine.style/experiments/wglq24">Archerx</a>).</p>
<?php include("_navigation.php"); ?>

</body>

<script id="vertexShaderSource" type="x-shader/x-vertex">
attribute vec2 a_position;
attribute vec2 a_texCoord;

uniform vec2 u_resolution;
uniform float u_flipY;

varying vec2 v_texCoord;

void main() {
    // convert the rectangle from pixels to 0.0 to 1.0
    vec2 zeroToOne = a_position / u_resolution;
    // convert from 0->1 to 0->2
    vec2 zeroToTwo = zeroToOne * 2.0;
    // convert from 0->2 to -1->+1 (clipspace)
    vec2 clipSpace = zeroToTwo - 1.0;

    gl_Position = vec4(clipSpace * vec2(1, u_flipY), 0, 1);

    // pass the texCoord to the fragment shader
    // The GPU will interpolate this value between points.
    v_texCoord = a_texCoord;
}
</script>

<script id="fragmentShaderSource" type="x-shader/x-fragment">
precision mediump float;

const int rows = <?php echo $rows ?>;
vec4 layers[rows];

// our texture
uniform sampler2D u_image;
uniform vec2 u_textureSize;
uniform float u_dilation;

uniform float u_kernels[rows*rows];  // target value = u*rows + i
uniform float u_kernelsWeight[rows]; // target weight = u

// the texCoords passed in from the vertex shader.
varying vec2 v_texCoord;

vec2 position;
float x = 0.0; // counter
float y = 0.0; // counter

vec2 onePixel = vec2(1.0, 1.0) / u_textureSize;

void main() {

    position = vec2(-4.0, -4.0);
    for (int u = 0; u < rows; u++) {
        for (int i = 0; i < rows; i++) {
            
            layers[u] += texture2D(u_image, v_texCoord + onePixel * position * u_dilation) * u_kernels[u*rows+i];
            
            
            { // calculate next vec2 position to use
                // POS X                
                position.x += 1.0; // -3.00
                if (0.0 + x < position.x) {
                    x++;
                    if (4.0 < x) {
                        x = 0.0;
                    }
                    position.x = -4.0 + x;
                    
                    // POS Y
                    position.y += 1.0;
                    if (0.0 + y < position.y) {
                        y++;
                        if (4.0 < y) {
                            y = 0.0;
                        }
                        position.y = -4.0 + y;
                    }
                }
            }
        }
    }
    
    position = vec2(-2.0, -2.0);
    for (int u=0; u < rows; u++) {
        
        if((layers[u].r / u_kernelsWeight[u]) > 1.0){
            layers[u].r /= u_kernelsWeight[u];
        } else {
            layers[u].r = texture2D(u_image, v_texCoord + onePixel * position).r;
        }

        if((layers[u].g / u_kernelsWeight[u]) > 1.0){
            layers[u].g /= u_kernelsWeight[u];
        } else {
            layers[u].g = texture2D(u_image, v_texCoord + onePixel * position).g;
        }

        if((layers[u].b / u_kernelsWeight[u]) > 1.0){
            layers[u].b /= u_kernelsWeight[u];
        } else {
            layers[u].b = texture2D(u_image, v_texCoord + onePixel * position).b;
        }
        
        
        if(layers[u].r > 254.0){
            layers[u].r = texture2D(u_image, v_texCoord + onePixel * position).r;
        } 

        if(layers[u].g > 254.0){
            layers[u].g = texture2D(u_image, v_texCoord + onePixel * position).g;
        }

        if(layers[u].b > 254.0){
            layers[u].b = texture2D(u_image, v_texCoord + onePixel * position).b;
        }


        { // calculate next position to use :
            position.x += 1.0;
            if (2.0 < position.x) {
                position.x = -2.0;
                position.y += 1.0;
                if (2.0 < position.y) {
                    position.y = -2.0;
                }
            }
        }
    }
    
    vec4 colorSum;
    for (int u = 0; u < rows; u++) {
        colorSum += layers[u];
    }
    colorSum += texture2D(u_image, v_texCoord + onePixel * (vec2( 0, 0)));
    colorSum /= vec4(rows);
    colorSum /= 2.0;
    
    if(colorSum.r < 0.01){
        colorSum.r = texture2D(u_image, v_texCoord + onePixel * (vec2(0.0, 0.0))).r; //0.01;
    }
    if(colorSum.g < 0.01){
        colorSum.g = texture2D(u_image, v_texCoord + onePixel * (vec2(0.0, 0.0))).g; //0.01;
    }
    if(colorSum.b < 0.01){
        colorSum.b = texture2D(u_image, v_texCoord + onePixel * (vec2(0.0, 0.0))).b; //0.01;
    }
    
    gl_FragColor = vec4((colorSum).rgb, 1.0);
}
</script>

<script>
var canvas = document.getElementById("glcanvas");
var ctx = canvas.getContext('2d');
var gl;
var image;
function main() {
    document.getElementById("progress").innerHTML = "Getting inspired...";
    
    // remove precedant tempGL instance
    if (document.getElementById("tempGL")) {
        document.getElementById("tempGL").remove();
    }
    // create GL canva and context webgl as gl
    let newCanva = document.createElement('canvas');
    newCanva.id = "tempGL";
    newCanva.style.display = "none";

    let body = document.getElementsByTagName("body")[0];
    body.appendChild(newCanva);

    gl = newCanva.getContext("webgl", {
        preserveDrawingBuffer: true
    });
    if (!gl) {
        console.log("---> Something went wrong <---");
        document.getElementById("glversion").innerHTML = "webGL not supported.";
    } else {
        document.getElementById("glversion").innerHTML = gl.getParameter(gl.VERSION);
        document.getElementById("glslversion").innerHTML = gl.getParameter(gl.SHADING_LANGUAGE_VERSION);
    }
    
    image = new Image();
    image.src = "img/quantum.png";
    image.onload = function() {
        //console.log("image loaded");
        canvas.height = this.height;
        canvas.width = this.width;
        document.getElementById("tempGL").height = this.height;
        document.getElementById("tempGL").width = this.width;
        
        kpi.init();
        startGL(); // start gl program info
        window.requestAnimationFrame(render);   
    };
}

// Kernel Program Information (kpi)
var kpi = {
    rows : <?php echo $rows ?>, // size of the matrix rows*rows
    octaves : 6,
    updateOctaves : function () {
        this.octaves = Math.floor(Math.random() * 6) + 3;
        document.getElementById("octaves").innerHTML = this.octaves;
        return this.octaves;
    },
    dilation : 0, // 511 was the original value
    updateDilation : function () {
        //this.dilation = Math.floor(Math.random() * 512) + 256;
        this.dilation = Math.floor(Math.random() * 1000000) + 1000000;
        document.getElementById("dilation").innerHTML = this.dilation;
        return this.dilation;
    },
    matrix : {
        kernels : [], // [u*rows+i]
        weights : []  // [u]
    },
    updateMatrix : function() {
        // transform kernels and weight locations as two array
        for (let u = 0; u < this.rows; u++) {
            let loop = this.matrixArrayGen();
            for (let i = 0; i < this.rows; i++) {
                // kernels array has a length of rows*rows (index u*rows)
                this.matrix.kernels[u*this.rows + i] = loop[i];
            }
            // weigth of each rows
            this.matrix.weights[u] = this.matrixArrayWeight(loop);
        }
        //console.log(this.matrix);
        return this.matrix;
    },
    matrixArrayGen : function () {
        let newkern = [];
        for (let i = 0; i < this.rows; i++) {
            newkern[i] = Math.floor((Math.random() * 50) - 25);
        }
        return newkern;
    },
    matrixArrayWeight : function(kernel) {
        let weight = kernel.reduce(function(prev, curr) {
            return prev + curr;
        });
        if (weight <= 0) {
            weight = 1;
        }
        return Math.floor(weight);
    },
    
    init : function () {
        document.getElementById("rows_info").innerHTML = this.rows;
        this.updateOctaves();
        this.updateDilation();
        this.updateMatrix();
    }
    
};


// startGL() function will create and set the GL program (glp) :
var glp = {};
function startGL() { 
    //console.log('starting the webGL program.');

    function createShader(gl, type, source) {
        let shader = gl.createShader(type);
        gl.shaderSource(shader, source);
        gl.compileShader(shader);
        let success = gl.getShaderParameter(shader, gl.COMPILE_STATUS);
        if (success) {
            return shader;
        }
        console.log(gl.getShaderInfoLog(shader));
        gl.deleteShader(shader);
    };
    function createProgram(gl, vertexShader, fragmentShader) {
        let program = gl.createProgram();
        gl.attachShader(program, vertexShader);
        gl.attachShader(program, fragmentShader);
        gl.linkProgram(program);
        let success = gl.getProgramParameter(program, gl.LINK_STATUS);
        if (success) {
            return program;
        }
        console.log(gl.getProgramInfoLog(program));
        gl.deleteProgram(program);
    };
    // create the vertex shader
    let vsc = document.getElementById("vertexShaderSource");
    let vs = vsc.text;
    let vertexShader = createShader(gl, gl.VERTEX_SHADER, vs);
    // create the fragment shader
    let fsc = document.getElementById("fragmentShaderSource");
    let fs = fsc.text;
    let fragmentShader = createShader(gl, gl.FRAGMENT_SHADER, fs);
    // create the program
    program = createProgram(gl, vertexShader, fragmentShader);
    
    // GL Program infos
    glp = {
        program : program,
        frame : 0,
        fps: 30,
        attribLocations : {
            position : gl.getAttribLocation(this.program, "a_position"),
            texCoord : gl.getAttribLocation(this.program, "a_texCoord")
        },
        uniformLocations : {
            resolution : gl.getUniformLocation(this.program, "u_resolution"),
            textureSize : gl.getUniformLocation(this.program, "u_textureSize"),
            flipY : gl.getUniformLocation(this.program, "u_flipY"),
            dilation : gl.getUniformLocation(this.program, "u_dilation"),
            rows : gl.getUniformLocation(this.program, "u_rows"),
            kernels : [],
            kernelsWeight : [] 
        },
        buffers : {
            position : gl.createBuffer(),
            texCoord : gl.createBuffer(),
            frame : []
        },
        textures : [],
        // create kernel and weight uniform locations as two array
        buildKernels : function () {
            for (let u=0; u < kpi.rows; u++) {
                for (let i=0; i < kpi.rows; i++) {
                    this.uniformLocations.kernels[u*kpi.rows + i] = gl.getUniformLocation(this.program, "u_kernels["+(u*kpi.rows + i)+"]");
                }
                this.uniformLocations.kernelsWeight[u] = gl.getUniformLocation(this.program, "u_kernelsWeight["+u+"]");
            }
        },
        // fill kernels and weight uniform values
        updateKernels : function () {
            kpi.updateMatrix();
            for (let u=0; u < kpi.rows; u++) {
                for (let i=0; i < kpi.rows; i++) {
                    gl.uniform1f(this.uniformLocations.kernels[u*kpi.rows + i], kpi.matrix.kernels[u*kpi.rows + i]);
                }
                gl.uniform1f(this.uniformLocations.kernelsWeight[u], kpi.matrix.weights[u]);
            }
        }
    };
    
    // Clear the canvas
    gl.clearColor(0, 0, 0, 0);
    gl.clear(gl.COLOR_BUFFER_BIT);
    // Tell it to use our program (pair of shaders)
    gl.useProgram(glp.program);
    
    // Create a buffer to put three 2d clip space points in
    // Bind it to ARRAY_BUFFER (think of it as ARRAY_BUFFER = glp.buffers.position)
    gl.bindBuffer(gl.ARRAY_BUFFER, glp.buffers.position);
    // Set a rectangle the same size as the image.
    let x1 = 0;
    let x2 = image.width;
    let y1 = 0;
    let y2 = image.height;
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([
        x1, y1,
        x2, y1,
        x1, y2,
        x1, y2,
        x2, y1,
        x2, y2
    ]), gl.STATIC_DRAW);

    // provide texture coordinates for the rectangle.
    gl.bindBuffer(gl.ARRAY_BUFFER, glp.buffers.texCoord);
    gl.bufferData(gl.ARRAY_BUFFER, new Float32Array([
        0.0, 0.0,
        1.0, 0.0,
        0.0, 1.0,
        0.0, 1.0,
        1.0, 0.0,
        1.0, 1.0
    ]), gl.STATIC_DRAW);
    
    
    document.getElementById("progress").innerHTML = "Drawing...";
    
    // Start the machine
    glp.buildKernels();
    glp.updateKernels();
    // don't y flip images while drawing to the textures ???
    gl.uniform1f(glp.uniformLocations.flipY, -1); // ???
        
    // Turn on the position attribute
    gl.enableVertexAttribArray(glp.uniformLocations.position);
    // Bind the position buffer.
    gl.bindBuffer(gl.ARRAY_BUFFER, glp.buffers.position);
    // Tell the position attribute how to get data out of glp.buffers.position (ARRAY_BUFFER)
    let sizePos = 2; // 2 components per iteration
    let typePos = gl.FLOAT; // the data is 32bit floats
    let normalizePos = false; // don't normalize the data
    let stridePos = 0; // 0 = move forward size * sizeof(type) each iteration to get the next position
    let offsetPos = 0; // start at the beginning of the buffer
    gl.vertexAttribPointer(glp.uniformLocations.position, sizePos, typePos, normalizePos, stridePos, offsetPos);

    // Turn on the texCoord attribute
    gl.enableVertexAttribArray(glp.attribLocations.texCoord);
    // bind the texCoord buffer.
    gl.bindBuffer(gl.ARRAY_BUFFER, glp.buffers.texCoord);
    // Tell the texCoord attribute how to get data out of texCoordBuffer (ARRAY_BUFFER)
    let size = 2; // 2 components per iteration
    let type = gl.FLOAT; // the data is 32bit floats
    let normalize = false; // don't normalize the data
    let stride = 0; // 0 = move forward size * sizeof(type) each iteration to get the next position
    let offset = 0; // start at the beginning of the buffer
    gl.vertexAttribPointer(glp.attribLocations.texCoord, size, type, normalize, stride, offset);
    
    for (var i = 0; i < 2; i++) {
        let texture = createAndSetupTexture(gl);
        glp.textures.push(texture);
        // make the texture the same size as the image
        gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, image.width, image.height, 0, gl.RGBA, gl.UNSIGNED_BYTE, null);
        // Create a framebuffer
        var fbo = gl.createFramebuffer();
        glp.buffers.frame.push(fbo);
        gl.bindFramebuffer(gl.FRAMEBUFFER, fbo);
        // Attach a texture to it.
        gl.framebufferTexture2D(gl.FRAMEBUFFER, gl.COLOR_ATTACHMENT0, gl.TEXTURE_2D, texture, 0);
    }
}

function render() {
    //console.log("Rendering...");
    glp.frame++;
    document.getElementById("frame").innerHTML = glp.frame;
    
        
    let primitiveType = gl.TRIANGLES;
    let offset = 0;
    let count = 6;
    
    let dilation = Math.floor(Math.random()*1000000) + 1000000;
    
    for (var i = 0; i < kpi.octaves; i++) {
        // make this the framebuffer we are rendering to.
        gl.bindFramebuffer(gl.FRAMEBUFFER, glp.buffers.frame[i % 2]);
        
        //let dilation = (i + 1) * 128;
        gl.uniform1f(glp.uniformLocations.dilation, dilation);
        gl.drawArrays(primitiveType, offset, count);
        
        // for the next draw, use the texture we just rendered to.
        gl.bindTexture(gl.TEXTURE_2D, glp.textures[i % 2]);
    }
    
    // Create a texture and put the image in it.
    let renderedlImageTexture = createAndSetupTexture(gl);
    gl.texImage2D(gl.TEXTURE_2D, 0, gl.RGBA, gl.RGBA, gl.UNSIGNED_BYTE, image);

    // set the size of the image
    gl.uniform2f(glp.uniformLocations.textureSize, image.width, image.height);
    // start with the original image
    gl.bindTexture(gl.TEXTURE_2D, renderedlImageTexture);
    
    gl.uniform1f(glp.uniformLocations.dilation, kpi.dilation);

    // make this the framebuffer we are rendering to.
    gl.bindFramebuffer(gl.FRAMEBUFFER, null);
    // Tell the shader the resolution of the framebuffer.
    gl.uniform2f(glp.uniformLocations.resolution, gl.canvas.width, gl.canvas.height);
    // Tell webgl the viewport setting needed for framebuffer.
    gl.viewport(0, 0, gl.canvas.width, gl.canvas.height);

    // Draw the rectangles.
    gl.drawArrays(primitiveType, offset, count);

    ctx.drawImage(document.getElementById("tempGL"), 0, 0);
    
    // Color sum
    let colorsum = getColorAverage(); //average(pixels);
    document.getElementById("colorsum").innerHTML = colorsum;

    if (colorsum < 250 && colorsum > 10) {
        // Render next frame
        image = document.getElementById("tempGL");
        window.requestAnimationFrame(render);
    } else {
        // Restart the all process
        document.getElementById("progress").innerHTML = "Failed";
        setTimeout(function() {
            main();
        }, 500);
    }
}

function createAndSetupTexture(gl) {
    let texture = gl.createTexture();
    gl.bindTexture(gl.TEXTURE_2D, texture);
    // Set up texture so we can render any size image and so we are
    // working with pixels.
    //MIRRORED_REPEAT
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_S, gl.REPEAT);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_WRAP_T, gl.REPEAT);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MIN_FILTER, gl.NEAREST);
    gl.texParameteri(gl.TEXTURE_2D, gl.TEXTURE_MAG_FILTER, gl.NEAREST);
    return texture;
}

function getColorAverage() {
    let test = 0;
    let average = 0;
    
    let pixels = new Uint8Array(gl.drawingBufferWidth * gl.drawingBufferHeight * 4);
    gl.readPixels(0, 0, gl.drawingBufferWidth, gl.drawingBufferHeight, gl.RGBA, gl.UNSIGNED_BYTE, pixels);
    
    for (let i = 0; i < pixels.length; i += 32) {
        average = average + pixels[i] + pixels[i + 1] + pixels[i + 2];
        test = test + 3;
    }
    return (average / test).toFixed(0);
}

main();
</script>
</html>
