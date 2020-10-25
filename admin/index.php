<?php

require dirname(__DIR__, 3) . '/include/cp_header.php';

define('MOD_PATH', XOOPS_ROOT_PATH . '/modules/PubMedPDF');
define('MOD_URL', XOOPS_URL . '/modules/PubMedPDF');
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

$mode = $_GET['mode'] ?? '';

xoops_cp_header();
switch ($mode) {
    case 'pdfdel':
        $pdf = $_GET['pdf'];
        echo '<b>' . _PA_DELPDF . '</b><br>';
        if (file_exists(WOPDFDIR . '/' . $pdf)) {
            if (unlink(WOPDFDIR . '/' . $pdf)) {
                echo $pdf . ' ' . _PA_DELETEDPDF . '<br><br>';
            } else {
                echo "<span style='color:red'>" . _PA_FALSE . '</span> ' . $pdf . ' ' . _PA_DELETEDPDF . '<br><br>';
            }
        } else {
            echo _PA_NOEXIST . '<br><br>';
        }

        // no break
    case 'check':
        require dirname(__DIR__) . '/include/xml.php';
        require __DIR__ . '/checkfiles.php';
        break;
    default:
        $url = XOOPS_URL . '/modules/system/admin.php?';
        echo "<b><a href='" . $url . 'fct=preferences&amp;op=showmod&amp;mod=' . $xoopsModule->getVar('mid') . "'>" . _PA_CONFIG . '</a></b><br>';
        echo "<b><a href='?mode=check'>" . _PA_CHECK . '</a></b>';
}
xoops_cp_footer();
