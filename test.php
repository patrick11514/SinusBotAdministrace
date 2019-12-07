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
$password = "test";

echo "hash normal - " . password_hash($password, PASSWORD_BCRYPT) . "<br>";
echo "better hash - " . password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));

?>
