<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/class/description.php';
require __DIR__ . '/class/favorite.php';
require XOOPS_ROOT_PATH . '/header.php';

if (!isset($_GET['id'])) {
    redirect_header(MOD_URL, 2, _MD_WRONGACCESS);
}

$id = (int)$_GET['id'];
$desc = new description();
if (!$data = $desc->getPaperInfo($id)) {
    redirect_header(MOD_URL, 2, _MD_DOSENTEXIST);
}

# pdf upload
if (isset($_POST['upload'])) {
    if ($data['pmid'] > 0) {
        $upname = PDFDIR . '/' . $data['pmid'] . '.pdf';
    } else {
        $upname = WOPDFDIR . '/' . $data['c_t1'] . '.pdf';
    }

    if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
        $file_name = $_FILES['userfile']['name'];

        if (preg_match("/.*(\.pdf)$/i", $file_name)) {
            move_uploaded_file($_FILES['userfile']['tmp_name'], $upname);
        }
    }
}

# favorite
if (isset($_POST['favorite'])) {
    $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_favorite_data');

    $sql .= " VALUES('','" . $id . "','" . (int)$_POST['dir'] . "','" . $user . "','')";

    $res = $xoopsDB->query($sql);

    # favorite counter

    $sql = 'SELECT F_num FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $n = $row['F_num'] + 1;

    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_id') . " SET F_num='" . $n . "' WHERE id='" . $id . "'";

    $res = $xoopsDB->query($sql);
}

$myts = MyTextSanitizer::getInstance();

echo "<table class='outer'>";
if ($data['pmid'] > 0) {
    echo "<tr><td class='head' style='width:120px'>PMID</td><td class='even'>";

    echo "<a  target='_blank' href='http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=search&term=" . $data['pmid'] . "'>" . $data['pmid'] . '</a>';

    echo '</td></tr>';
}
echo "<tr><td class='head' style='width:120px'>" . _MD_TITLE . "</td><td class='even'>" . htmlspecialchars($data['title'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
if (!empty($data['title_jp'])) {
    echo "<tr><td class='head'>" . _MD_TITLE . '(' . _MD_JP . ")</td><td class='even'>" . htmlspecialchars($data['title_jp'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
}
echo "<tr><td class='head'>" . _MD_AUTHOR . "</td><td class='even'>" . htmlspecialchars($data['author'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
if (!empty($data['author_jp'])) {
    echo "<tr><td class='head'>" . _MD_AUTHOR . '(' . _MD_JP . ")</td><td class='even'>" . htmlspecialchars($data['author_jp'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
}
echo "<tr><td class='head'>" . _MD_JB . "</td><td class='even'>" . htmlspecialchars($data['journal'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
if (!empty($data['journal_jp'])) {
    echo "<tr><td class='head'>" . _MD_JB . '(' . _MD_JP . ")</td><td class='even'>" . htmlspecialchars($data['journal_jp'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
}
echo "<tr><td class='head'>" . _MD_YEAR . "</td><td class='even'>" . (int)$data['year'] . '</td></tr>';
if (!empty($data['v']) || !empty($data['pp'])) {
    echo "<tr><td class='head'>" . _MD_VP . "</td><td class='even'>" . htmlspecialchars($data['v'], ENT_QUOTES | ENT_HTML5) . ' / ' . htmlspecialchars($data['pp'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
}
if (!empty($data['abst'])) {
    echo "<tr><td class='head'>" . _MD_ABST . "</td><td class='even'>" . htmlspecialchars($data['abst'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';
}
echo "<tr><td class='head'>" . _MD_INFO . "</td><td class='even'>" . $data['reg_usr'] . ' (' . $data['reg_date'] . ')</td></tr>';

# remove/edit wopmid
if (($data['pmid'] < 0) && ($data['reg_usr'] == $user || $isadmin)) {
    echo "<tr><td class='head'>" . _MD_NOTE_EDIT_C . "</td><td class='even'>";

    echo "<a href='edit_wopmid.php?id=" . $id . "'>" . _MD_EDITINFO . '</a>';

    echo '</td></tr>';
}

echo "</table><table class='outer' style='margin-top:15px'>";

# xml / pdf upload
if ($data['pmid'] > 0) {
    $xname = PXMLDIR . '/' . $data['pmid'] . '.xml';

    $pname = PDFDIR . '/' . $data['pmid'] . '.pdf';
} else {
    $xname = UPXML . '/' . $data['c_t1'] . '.xml';

    $pname = WOPDFDIR . '/' . $data['c_t1'] . '.pdf';
}

if (file_exists(MOD_PATH . '/' . $xname)) {
    echo "<tr><td class='head'>XML</td><td class='even'>";

    echo "<a href='" . MOD_URL . '/' . $xname . "' target='_blank'>XML</a>";

    echo '</td></tr>';
}

# upload
if (!file_exists($pname)) {
    echo "<tr><td class='head'>PDF</td><td class='even'>";

    echo "<form enctype='multipart/form-data' action='?id=" . $id . "' method='POST'>";

    echo "<input type='file' name='userfile'>";

    echo "<input type='submit' name='upload' value='submit'>";

    echo '</form>';

    echo '</td></tr>';
}

# favorite
echo "<tr><td class='head' style='width:120px'>" . _MD_FAVORITE . "</td><td class='even'>";
$sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_favorite_data');
$sql .= " WHERE data_id='" . $id . "' AND Usr='" . $user . "'";
$res = $xoopsDB->query($sql);
if ($n = $xoopsDB->getRowsNum($res)) {
    echo _MD_FAVO_ALREADY . '<br><br>';
}
echo "<form action='?id=" . $id . "' method='POST'>";
echo " INSERT INTO <SELECT NAME='dir'>";
$fv = new favorite($user);
echo $fv->getDirList($id);
echo '</select>';
echo " <input type='submit' value='submit' name='favorite'>";
echo '</form>';
echo '</td></tr>';

echo "</table><br><a href='" . MOD_URL . "'>HOME</a>";
require XOOPS_ROOT_PATH . '/footer.php';
