<?php
@ini_set('zlib.output_compression',0);
@ini_set('implicit_flush',1);
@ini_set('output_buffering', 'Off');
@ini_set('output_handler', '');
@ini_set('implicit_flush','On');
@ob_implicit_flush(1);
@ob_end_clean();

header('Content-Encoding: none');
header("Cache-Control: no-cache, must-revalidate");
header('X-Accel-Buffering: no');

for($i=0; $i<10; $i++){
    echo $i;

    //this is for the buffer achieve the minimum size in order to flush data
    echo str_pad("",1024," ");
    echo str_repeat(' ',1024*64);
    ob_flush();
    flush();
    sleep(1);
}


?>
