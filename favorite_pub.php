<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/class/favorite.php';
require __DIR__ . '/class/description.php';

$fv = new favorite($user);
$desc = new description($user);

$dir_id = 0;
$dirname = '';
# directory info
if (isset($_GET['dir_id'])) {
    $dir_id = (int)$_GET['dir_id'];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_favorite_dir');

    $sql .= " WHERE id='" . $dir_id . "' AND Public_flg='1'";

    $res = $xoopsDB->query($sql);

    if (!$xoopsDB->getRowsNum($res)) {
        $dir_id = 0;
    } else {
        $row = $xoopsDB->fetchArray($res);

        $dirname = ' / ' . $row['Name'];
    }
}

$dirs = $fv->getPublicDir();
if (0 == $dir_id) {
    $data = $fv->getPublicData();
} else {
    $data = $fv->getData($dir_id, 'pub');
}

require XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/style.php';

echo "<div class='pt'>" . _MD_NOTE_PUB . _MD_FAVORITE_DIRLIST . '</div>' . $dirs;
echo "<div class='pt'><a href='favorite_pub.php'>" . _MD_FAVORITE_ALL . '</a>' . $dirname . '</div>';
echo $desc->getPaper($data[0], PDFDIR2);

require XOOPS_ROOT_PATH . '/footer.php';
