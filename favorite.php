<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/class/favorite.php';
require __DIR__ . '/class/description.php';

$fv = new favorite($user);
$desc = new description($user);

$dir_id = 0;
$path = '';
$public = 0;

# directory info
if (isset($_GET['dir_id'])) {
    $dir_id = (int)$_GET['dir_id'];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_favorite_dir');

    $sql .= " WHERE id='" . $dir_id . "' AND Usr='" . $user . "'";

    $res = $xoopsDB->query($sql);

    if (!$xoopsDB->getRowsNum($res)) {
        $dir_id = 0;
    } else {
        $row = $xoopsDB->fetchArray($res);

        $path = $row['Pass'];

        $public = $row['Public_flg'];
    }
}

# public
if (isset($_GET['public']) && 'y' == $_GET['public']) {
    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_favorite_dir');

    $sql .= " SET Public_flg='1' WHERE id='" . $dir_id . "'";

    $res = $xoopsDB->queryF($sql);

    $public = 1;
} elseif (isset($_GET['public']) && 'n' == $_GET['public']) {
    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_favorite_dir');

    $sql .= " SET Public_flg='0' WHERE id='" . $dir_id . "'";

    $res = $xoopsDB->queryF($sql);

    $public = 0;
}

# mkdir
if (isset($_POST['dirname']) && !empty($_POST['dirname'])) {
    $dirname = addslashes($myts->stripSlashesGPC(@$_POST['dirname']));

    $path4sql = str_replace('[0]', '', $path . '[' . $dir_id . ']');

    $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_favorite_dir');

    $sql .= " VALUES('','" . $user . "','" . $dirname . "','" . $path4sql . "','')";

    $res = $xoopsDB->query($sql);
}

# rmdir
if (isset($_GET['del_id'])) {
    $del = [];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_favorite_dir');

    $sql .= " WHERE id='" . (int)$_GET['del_id'] . "'";

    $res = $xoopsDB->query($sql);

    if (!$xoopsDB->getRowsNum($res)) {
        break;
    }

    $del[] = (int)$_GET['del_id'];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_favorite_dir');

    $sql .= " WHERE Pass like '%[" . $del[0] . "]%' AND Usr='" . $user . "'";

    $res = $xoopsDB->query($sql);

    while (false !== ($row = $xoopsDB->fetchArray($res))) {
        $del[] = $row['id'];
    }

    for ($i = 0, $iMax = count($del); $i < $iMax; $i++) {
        $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_favorite_data');

        $sql .= " SET dir_id='0' WHERE dir_id='" . $del[$i] . "'";

        $res = $xoopsDB->queryF($sql);
    }

    for ($i = 0, $iMax = count($del); $i < $iMax; $i++) {
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_favorite_dir');

        $sql .= " WHERE id='" . $del[$i] . "'";

        $res = $xoopsDB->queryF($sql);
    }
}

# delete/move
if (isset($_POST['data'])) {
    foreach ($_POST['data'] as $v) {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_favorite_data');

        $sql .= " WHERE id='" . $v . "' AND Usr='" . $user . "'";

        $res = $xoopsDB->query($sql);

        if (!$xoopsDB->getRowsNum($res)) {
            break;
        }

        $row = $xoopsDB->fetchArray($res);

        switch ($_POST['mv']) {
            case 'insert':
                $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_favorite_data');
                $sql .= " SET dir_id='" . (int)$_POST['moveto'] . "' WHERE id='" . $v . "'";
                $res = $xoopsDB->query($sql);
                break;
            case 'delete':
                $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_id');
                $sql .= " SET F_num=(F_num-1) WHERE id='" . $row['data_id'] . "'";
                $res = $xoopsDB->query($sql);

                $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_favorite_data') . " WHERE id='" . $v . "'";
                $res = $xoopsDB->query($sql);
                break;
            case 'biblio':
                #xoops_pmid_tmp (biblio, user, dataid, )
                $dataid = $row['data_id'];
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');
                $sql .= " WHERE tmp1='biblio' AND tmp2='" . $user . "' AND tmp3='" . $dataid . "'";
                $res = $xoopsDB->query($sql);
                if (!$xoopsDB->getRowsNum($res)) {
                    $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_tmp');

                    $sql .= " VALUES('biblio','" . $user . "','" . $dataid . "','')";

                    $res = $xoopsDB->query($sql);
                }
                break;
        }
    }
}

require XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/style.php';
echo <<<E
	<script>
	function checkFData(pref, flg){
		if (! document.getElementById) return;
		for (i = 0;; i++) {
			if (! document.getElementById(pref + i)) break;
			if(flg == '1'){
				document.getElementById(pref + i).checked = true;
			}else{
				document.getElementById(pref + i).checked = false;
			}
		}
	}
	</script>
E;

$dirs = $fv->getDir($dir_id);
$data = $fv->getData($dir_id);
$dir_list = $fv->getDirList();

echo "<div class='pt'>" . _MD_FAVORITE_MKDIR . '</div>';
echo "<form actin='?dir_id=" . $dir_id . "' method='POST'>";
echo "<input type='text' name='dirname'>";
echo "<input type='submit' value='submit'>";
echo '</form>';

echo "<div class='pt' style='margin-top:20px'>" . _MD_FAVORITE_DIRLIST . '</div>' . $dirs;
echo "<div class='pt' style='margin-top:20px'>" . $fv->getDirNavi($dir_id) . '</div>';

if ($dir_id) {
    echo "<div style='text-align:right'>";

    if (!$public) {
        echo "<a href='?dir_id=" . $dir_id . "&public=y'>" . _MD_FAVORITE_PUB . '</a>';
    } else {
        echo "<a href='?dir_id=" . $dir_id . "&public=n'>" . _MD_FAVORITE_PRI . '</a>';
    }

    echo '</div>';
}

echo "<a href=\"javascript:checkFData('data', 1)\">" . _MD_CHECK_ALL . '</a> / ';
echo "<a href=\"javascript:checkFData('data', 0)\">" . _MD_FAVORITE_FREE . '</a> ';

echo "<form action='?dir_id=" . $dir_id . "' method='POST' style='margin-top:10px'>";
echo $desc->getPaper($data[0], PDFDIR2, $data[1]);

echo "<br><input type='radio' name='mv' value='insert' checked>";
if (_MD_INSERTINTO == 'insert into') {
    echo _MD_INSERTINTO . ' ' . "<select name='moveto'>" . $dir_list . '</select> ';
} else {
    echo "<select name='moveto'>" . $dir_list . '</select> ' . _MD_INSERTINTO;
}
echo "<br><input type='radio' name='mv' value='biblio'>" . _MD_ADD_BIBLIO;
echo "<br><input type='radio' name='mv' value='delete'>" . _MD_DELETE . ' ';

echo "<br><br><input type='submit' value='submit'>";
echo '</form>';

require XOOPS_ROOT_PATH . '/footer.php';
