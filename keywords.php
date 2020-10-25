<?php

require __DIR__ . '/nheader.php';

if (isset($_POST['method'])) {
    switch ($_POST['method']) {
        case 'new':
            # tmp(psearch, id, title::keyword, user)
            for ($id = 1; ; $id++) {
                $sql = 'SELECT tmp2 FROM ' . $xoopsDB->prefix('pmid_tmp');

                $sql .= " WHERE tmp1='psearch' AND tmp2='" . $id . "'";

                $rs = $xoopsDB->query($sql);

                if (!$xoopsDB->getRowsNum($rs)) {
                    break;
                }
            }
            $title = addslashes($myts->stripSlashesGPC($_POST['title']));
            $keyword = addslashes($myts->stripSlashesGPC($_POST['keyword']));

            $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_tmp');
            $sql .= " VALUES('psearch','" . $id . "','" . $title . '::' . $keyword . "','" . $user . "')";
            $res = $xoopsDB->query($sql);
            break;
        case 'edit':
            $id = (int)$_POST['id'];
            $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');
            $sql .= " WHERE tmp1='psearch' AND tmp2='" . $id . "' AND tmp4='" . $user . "'";
            $res = $xoopsDB->query($sql);
            if ($xoopsDB->getRowsNum($res)) {
                $title = addslashes($myts->stripSlashesGPC($_POST['title']));

                $keyword = addslashes($myts->stripSlashesGPC($_POST['keyword']));

                if (empty($title) && empty($keyword)) {
                    $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_tmp');

                    $sql .= " WHERE tmp1='psearch' AND tmp2='" . $id . "'";

                    $res = $xoopsDB->query($sql);
                } else {
                    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_tmp');

                    $sql .= " SET tmp3='" . $title . '::' . $keyword . "' WHERE tmp1='psearch' AND tmp2='" . $id . "'";

                    $res = $xoopsDB->query($sql);
                }
            }
            break;
    }
}

require XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/style.php';
echo "<div class='pt'>" . _MD_PubMedCreatekeyword . '</div>';
showForm();
echo "<div style='text-align:center'><BR><a href='search.php'>" . _MD_PubMedreturn . '</a></div>';
echo "<div class='pt' style='margin-top:10px'>" . _MD_PubMedEditkeyword . '</div>';
echo "<div style='text-align:center; margin-bottom:10px'>" . _MD_MKEYDEL . '</div>';

$sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');
$sql .= " WHERE tmp1='psearch' AND tmp4='" . $user . "'";
$res = $xoopsDB->query($sql);
while (false !== ($row = $xoopsDB->fetchArray($res))) {
    $token = explode('::', $row['tmp3']);

    showForm('edit', $row['tmp2'], $token[0], $token[1]);
}
require XOOPS_ROOT_PATH . '/footer.php';

function showForm($method = 'new', $id = 0, $title = '', $keyword = '')
{
    echo "<form enctype='multipart/form-data' action='' method='POST'>";

    echo '<center>';

    echo "<table class='outer' style='width:80%'>";

    echo "<tr><td class='head' style='width:40px'>Title</td>";

    echo "<td class='even'><input type='text' style='width:250px' name='title' value='" . $title . "'></td>";

    echo '</tr>';

    echo "<tr><td class='head'>Keywords</td>";

    echo "<td class='even'><input type='text' style='width:95%' name='keyword' value='" . $keyword . "'>";

    echo '</tr>';

    echo "<tr><td class='head'> </td>";

    echo "<td class='even'><input type='submit' value='submit'></td></tr>";

    echo '</table></center>';

    if ('edit' == $method) {
        echo "<input type='hidden' name='id' value='" . $id . "'>";
    }

    echo "<input type='hidden' name='method' value='" . $method . "'>";

    echo '</form>';
}
