<?php

require dirname(__DIR__, 2) . '/mainfile.php';

define('MOD_PATH', XOOPS_ROOT_PATH . '/modules/PubMedPDF');
define('MOD_URL', XOOPS_URL . '/modules/PubMedPDF');
define('PERMITTED', $xoopsModuleConfig['suffix']);

define('UPDIR', 'up_files');
define('PXMLDIR', 'pubmed_xml');
define('SXMLDIR', 'search_xml');
define('UPXML', 'uploads_xml');

$pdfdir = $xoopsModuleConfig['updir'];
if ('/' == mb_substr($pdfdir, -1)) {
    $pdfdir = mb_substr($pdfdir, 0, -1);
}

$pdfdir2 = $xoopsModuleConfig['updir2'];
if ('/' == mb_substr($pdfdir2, -1)) {
    $pdfdir2 = mb_substr($pdfdir2, 0, -1);
}

define('PDFDIR', $pdfdir);
define('PDFDIR2', $pdfdir2);
define('WOPDFDIR', PDFDIR . '/wopmid');

$path = [UPDIR, PXMLDIR, SXMLDIR, UPXML, PDFDIR, WOPDFDIR];
for ($i = 0, $iMax = count($path); $i < $iMax; $i++) {
    if ($i < 4) {
        $p = MOD_PATH . '/' . $path[$i];
    } else {
        $p = $path[$i];
    }

    if (!is_dir($p) && !mkdir($p, 0777)) {
        redirect_header(XOOPS_URL, 2, 'fail to make directory ' . $p);
    }
}

if (is_object($xoopsUser)) {
    $user = $xoopsUser->getVar('uname');

    if ($xoopsUser->isAdmin($xoopsModule->mid())) {
        $isadmin = 1;
    } else {
        $isadmin = 0;
    }
} else {
    $user = 'guest';

    $isadmin = 0;
}
$myts = MyTextSanitizer::getInstance();
