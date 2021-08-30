    <p>
<?php
        $prev = (int)substr(basename($_SERVER['REQUEST_URI']), 5, -4);
        $file = "demo_".($prev - 1).".php";
        if (file_exists($file))
        {
            echo "\t\t<a href=\"./$file\">Previous</a> <span class=\"gray\">|</span> \r\n";
        }
?>
        <a href="./">Index</a>
<?php
        $next = (int)substr(basename($_SERVER['REQUEST_URI']), 5, -4);
        $file = "demo_".($next + 1).".php";
        if (file_exists($file))
        {
            echo "\t\t<span class=\"gray\">|</span> <a href=\"./$file\">Next</a>\r\n";
        }
?>
        
    </p>