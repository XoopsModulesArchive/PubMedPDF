<?php

require dirname(__DIR__, 3) . '/mainfile.php';
if (is_object($xoopsUser)) {
    $user = $xoopsUser->getVar('uname');
} else {
    $user = 'guest';
}

# favorite
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    if (!$xoopsDB->getRowsNum($res)) {
        redirect_header(MOD_URL, 2, _MD_DOSENTEXIST);
    }

    $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_favorite_data');

    $sql .= " VALUES('','" . $id . "','0','" . $user . "','')";

    $res = $xoopsDB->queryF($sql);

    # favorite counter

    $sql = 'SELECT F_num FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $n = $row['F_num'] + 1;

    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_id') . " SET F_num='" . $n . "' WHERE id='" . $id . "'";

    $res = $xoopsDB->queryF($sql);
}

require XOOPS_ROOT_PATH . '/header.php';
echo '<script>window.history.back();</script>';
require XOOPS_ROOT_PATH . '/footer.php';
