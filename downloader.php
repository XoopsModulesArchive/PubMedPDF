<?php

include '../../mainfile.php';

if ('wo' == @$_GET['dir']) {
    $archive = 'wo_xml';

    $dir = XOOPS_ROOT_PATH . '/modules/PubMedPDF/uploads_xml/';
} else {
    $archive = 'w_xml';

    $dir = XOOPS_ROOT_PATH . '/modules/PubMedPDF/pubmed_xml/';
}

if (false !== extension_loaded('zlib')) {
    if (@function_exists('gzcompress')) {
        require_once dirname(__DIR__, 2) . '/class/zipdownloader.php';

        $downloader = new XoopsZipDownloader();

        if ($handle = opendir($dir)) {
            while (false !== $file = readdir($handle)) {
                if ('.' != $file && '..' != $file) {
                    $downloader->addFile($dir . $file, $file);
                }
            }

            closedir($handle);
        }

        echo $downloader->download($archive, true);
    }
} else {
    echo 'error.';
}
