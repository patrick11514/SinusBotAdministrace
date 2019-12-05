<?php
/*@ini_set ('zlib.output_compression', 0);

@ini_set ('implicit_flush', 1);

@ob_end_clean ();

set_time_limit (0);

ob_implicit_flush(1);

for($i=0; $i<10; $i++){
    echo $i;

    //this is for the buffer achieve the minimum size in order to flush data
    echo str_repeat(' ',1024*64);

    sleep(1);
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
  <div id="log"></div>
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
  <script>
  $("#log").text("test");
  </script>
</body>
</html>