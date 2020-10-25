<?php

header('Content-disposition:attachment;filename=data.txt');
header('Content-type:application/octet-stream;name=data.txt');

if (file_exists('../datalist.txt')) {
    $fp = fopen('../datalist.txt', 'rb');

    while (!feof($fp)) {
        echo fgets($fp);
    }

    fclose($fp);

    unlink('../datalist.txt');
}
