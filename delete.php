<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/include/rm.php';

if ('guest' == $user) {
    redirect_header(MOD_URL, 2, _MD_NOTPERMITTED);
}

$id = (int)$_POST['id'];
$sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . " where PMID='" . $id . "'";
$res = $xoopsDB->query($sql);
if (!$xoopsDB->getRowsNum($res)) {
    redirect_header(MOD_URL, 2, 'PMID[' . $id . '] does not exist.');
}
$row = $xoopsDB->fetchArray($res);

if ($row['R_usr'] == $user || $isadmin) {
    $mes = _MD_DEL_DATA2;

    $sql = 'delete from ' . $xoopsDB->prefix('pmid_id') . " where PMID='" . $id . "'";

    $res = $xoopsDB->query($sql);

    if ($res) {
        $mes = _MD_DEL_DATA;

        if (file_exists(PDFDIR . '/' . $id . '.pdf')) {
            if (unlink(PDFDIR . '/' . $id . '.pdf')) {
                $mes .= _MD_DEL_PDF;
            } else {
                $mes .= _MD_DEL_PDF2;
            }
        }

        if (file_exists(MOD_PATH . '/' . PXMLDIR . '/' . $id . '.xml')) {
            if (unlink(MOD_PATH . '/' . PXMLDIR . '/' . $id . '.xml')) {
                $mes .= _MD_DEL_XML;
            } else {
                $mes .= _MD_DEL_XML2;
            }
        }

        rmJournal($row['Journal']);

        rmAuthor($row['Author']);
    }
} else {
    $mes = _MD_NOTPERMITTED;
}
redirect_header(MOD_URL, 2, $mes);
