<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>WebGL Demo</title>
    <link rel="stylesheet" href="./styles/style.css" type="text/css">
</head>

<body>
    <h1>WebGL Demo</h1>
    <nav id="glcanvas">
        <p>Let me try webGL ! All the code is available with ctrl-u. :-)</p>
        <ul>
<?php
$directory = scandir('./', 0);
foreach ($directory as $dir) {
    if(preg_match('/^demo_[0-9]+.php/', $dir))
    {
        echo "\t\t<li><a href='". $dir . "'>" . ucfirst(preg_replace("/_/", " ", substr($dir, 0, -4))) . "</a></li>\r\n";
    }
}
//var_dump($directory);
?>
        </ul>
    </nav>
</body>
</html>