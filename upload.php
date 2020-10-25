<?php

require __DIR__ . '/nheader.php';
require XOOPS_ROOT_PATH . '/class/snoopy.php';
require __DIR__ . '/include/pubmed.php';
require __DIR__ . '/include/db.php';
require __DIR__ . '/include/news.php';

$pubmed_xml = MOD_PATH . '/' . PXMLDIR;
$search_xml = MOD_PATH . '/' . SXMLDIR;
$up_pass = PDFDIR;
$error = '';
$mes = '';

## PDF registration
if (isset($_POST['upload'])) {
    if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
        $file_name = $_FILES['userfile']['name'];

        if (preg_match("/.*(\.pdf)$/i", $file_name)) {
            $PMID = (int)mb_substr($file_name, 0, -4);

            move_uploaded_file($_FILES['userfile']['tmp_name'], $up_pass . '/' . $PMID . '.pdf');

            $mes = 'File upload was completed.<br><br>';
        } else {
            $error = "error: This isn't PDF file.";
        }
    } else {
        $error = 'error: File upload failed.';
    }

    if ($error) {
        redirect_header(MOD_URL, 2, $error);
    }

    ## PMID registration
} elseif (isset($_POST['reg_by_id'])) {
    $PMID = (int)$_POST['pmid'];
}

## Check registration
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE PMID='" . $PMID . "'";
$res = $xoopsDB->query($sql);
if ($xoopsDB->getRowsNum($res)) {
    redirect_header(MOD_URL, 2, 'This PMID is already registered.');
}

## Proxy
if ($xoopsModuleConfig['proxy']) {
    $proxy = new Snoopy();

    $proxy->read_timeout = 0;

    $proxy->proxy_host = $xoopsModuleConfig['proxy_url'];

    $proxy->proxy_port = $xoopsModuleConfig['proxy_port'];
} else {
    $proxy = 0;

    $snoopy = new Snoopy();
}

## PubMed Search & Fetch

$pm_data = [];
if (file_exists($search_xml . '/' . $PMID . '.xml')) {
    $pm_data = PubMedByFile($PMID, $search_xml);

    rename($search_xml . '/' . $PMID . '.xml', $pubmed_xml . '/' . $PMID . '.xml');
} else {
    $pm_data = PubMed($PMID, $proxy, $snoopy, $pubmed_xml);

    if (!$pm_data) {
        if (file_exists($up_pass . '/' . $PMID . '.pdf')) {
            unlink($up_pass . '/' . $PMID . '.pdf');
        }

        redirect_header(MOD_URL, 2, 'No items found.');
    }
}

$id = DB($pm_data, $user);

if ($xoopsModuleConfig['news']) {
    NEWS_REGISTER('Reg.w.PMID', $pm_data['t'], $pm_data['ab'], $user);
}

if (isset($id)) {
    redirect_header(MOD_URL . '/paper_desc.php?id=' . $id, 2, _MD_REGISTERED);
} else {
    redirect_header(MOD_URL . '/register.php', 2, _MD_REGISTEREDERROR);
}
