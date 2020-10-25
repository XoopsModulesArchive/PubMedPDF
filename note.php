<?php

require __DIR__ . '/nheader.php';

$mid = 0;
if (isset($_GET['mid'])) {
    $mid = (int)$_GET['mid'];
} elseif (isset($_POST['mid'])) {
    $mid = (int)$_POST['mid'];
}

$nid = 0;
if (isset($_GET['nid'])) {
    $nid = (int)$_GET['nid'];
} elseif (isset($_POST['nid'])) {
    $nid = (int)$_POST['nid'];
}

if ($nid) {
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_memo') . " WHERE id='" . $nid . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $mid = $row['data_id'];

    if ($row['R_usr'] != $user) {
        redirect_header(MOD_URL, 2, _MD_NOTE_PERMISSION_ERROR);
    }
}

if (!$mid) {
    redirect_header(MOD_URL, 2, _MD_WRONGACCESS);
}

$mode = 'new';
if (isset($_POST['mode'])) {
    $mode = $_POST['mode'];
} elseif (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
}

switch ($mode) {
    case 'new':
        require XOOPS_ROOT_PATH . '/header.php';
        require __DIR__ . '/style.php';

        $mode = 'reg_new';
        showLinks();
        showNotes($mid, $user, PERMITTED);
        echo "<div class='pt' style='margin-top:20px;'>" . _MD_NOTE_NEW2 . '</div>';
        require __DIR__ . '/include/commentform.inc.php';
        echo '</div>';
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'reg_new':
        require __DIR__ . '/include/news.php';
        $day = getdate();
        $date = $day['year'] . '-' . $day['mon'] . '-' . $day['mday'];

        $flg = (int)$_POST['pflg'];
        $note = $myts->stripSlashesGPC($_POST['note']);
        $note4sql = addslashes($note);

        //memo register
        $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_memo');
        $sql .= " VALUES('','" . $mid . "','" . $user . "','" . $date . "','" . $note4sql . "','" . $flg . "')";
        $res = $xoopsDB->query($sql);
        $memo_id = $xoopsDB->getInsertId();

        //file upload
        $up_mes = '';
        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            $up_name = $_FILES['userfile']['name'];

            $suf = explode('.', $up_name);

            $suf = $suf[count($suf) - 1];

            $up_name = $memo_id . '.' . $suf;

            $perm = explode('|', PERMITTED);

            for ($i = 0, $iMax = count($perm); $i < $iMax; $i++) {
                $perm[$i] = mb_strtoupper($perm[$i]);
            }

            if (in_array(mb_strtoupper($suf), $perm, true)) {
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], MOD_PATH . '/' . UPDIR . '/' . $up_name)) {
                    $up_mes = '<br>' . _MD_NOTE_ATTACHED_ADDED;
                } else {
                    $up_mes = '<br>' . _MD_ATTACHERROR;
                }

                //xoops_pmid_tmp('upfile_name_for_memo','','file name','')

                $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_tmp');

                $sql .= " VALUES('upfile_name_for_memo','','" . $up_name . "','')";

                $res = $xoopsDB->query($sql);
            } else {
                $up_mes = '<br>' . _MD_SUFFIXERROR;
            }
        }

        //news register
        if ($xoopsModuleConfig['news'] && $flg) {
            $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE pmid='" . $mid . "'";

            $res = $xoopsDB->query($sql);

            $row = $xoopsDB->fetchArray($res);

            $title = $row['Title'];

            NEWS_REGISTER('Note', $title, $note, $user);
        }

        redirect_header(MOD_URL . '/note.php?mid=' . $mid, 2, _MD_NOTEADDED . $up_mes);
        break;
    case 'edit':
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_memo') . " WHERE id='" . $nid . "'";
        $res = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($res);

        if ($row['R_usr'] == $user || $isadmin) {
            $note = $row['Comment'];

            $pflg = $row['Public_flg'];

            $mode = 'do_edit';

            require XOOPS_ROOT_PATH . '/header.php';

            require __DIR__ . '/style.php';

            echo "<div class='pt' style='margin-top:20px;'>" . _MD_NOTE_EDIT . '</div>';

            require __DIR__ . '/include/commentform.inc.php';

            echo '</div>';

            require XOOPS_ROOT_PATH . '/footer.php';
        } else {
            redirect_header(MOD_URL, 2, _MD_WRONGACCESS);
        }
        break;
    case 'do_edit':
        $day = getdate();
        $date = $day['year'] . '-' . $day['mon'] . '-' . $day['mday'];
        $flg = (int)$_POST['pflg'];
        $note = addslashes($myts->stripSlashesGPC($_POST['note']));

        $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_memo');
        $sql .= " SET R_date='" . $date . "', Comment='" . $note . "', Public_flg='" . $flg . "'";
        $sql .= " WHERE id='" . $nid . "'";
        $res = $xoopsDB->query($sql);

        $up_mes = '';
        if (isset($_POST['del_file'])) {
            $up_mes = delAttache($nid, MOD_PATH, UPDIR);
        } elseif (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            delAttache($nid, MOD_PATH, UPDIR);

            $up_name = $_FILES['userfile']['name'];

            $suf = explode('.', $up_name);

            $suf = $suf[count($suf) - 1];

            $up_name = $nid . '.' . $suf;

            $perm = explode('|', PERMITTED);

            for ($i = 0, $iMax = count($perm); $i < $iMax; $i++) {
                $perm[$i] = mb_strtoupper($perm[$i]);
            }

            if (in_array(mb_strtoupper($suf), $perm, true)) {
                if (move_uploaded_file($_FILES['userfile']['tmp_name'], MOD_PATH . '/' . UPDIR . '/' . $up_name)) {
                    $up_mes = '<br>' . _MD_NOTE_ATTACHED_ADDED;
                } else {
                    $up_mes = '<br>' . _MD_ATTACHERROR;
                }

                //xoops_pmid_tmp('upfile_name_for_memo','','file name','')

                $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_tmp');

                $sql .= " VALUES('upfile_name_for_memo','','" . $up_name . "','')";

                $res = $xoopsDB->query($sql);
            } else {
                $up_mes = '<br>' . _MD_SUFFIXERROR;
            }
        }

        redirect_header(MOD_URL . '/note.php?mid=' . $mid, 2, _MD_NOTE_EDITED . $up_mes);
        break;
    case 'delete':
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_memo') . " WHERE id='" . $nid . "'";
        $res = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($res);
        $c = $myts->displayTarea($row['Comment'], 0);

        if ($row['R_usr'] == $user || $isadmin) {
            require XOOPS_ROOT_PATH . '/header.php';

            require __DIR__ . '/style.php';

            echo "<div class='pt' style='margin-top:20px;'>" . _MD_NOTE_DEL . '</div>';

            echo "<table class='note'>";

            echo "<tr class='content'><td>" . $c . '</td></tr>';

            echo '</table>';

            echo "<div><a href='note.php?nid=" . $nid . "&mode=do_del'>YES</a> / <a href='javascript:history.go(-1)'>NO</a></div>";

            require XOOPS_ROOT_PATH . '/footer.php';
        } else {
            redirect_header(MOD_URL, 2, _MD_WRONGACCESS);
        }
        break;
    case 'do_del':
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_memo') . " WHERE id='" . $nid . "'";
        $res = $xoopsDB->queryF($sql);

        //uploaded file delete
        $up_mes = delAttache($nid, MOD_PATH, UPDIR);
        $mes = 'メモを排除しました。';
        if ($up_mes) {
            $mes .= $up_mes;
        }

        redirect_header(MOD_URL . '/note.php?mid=' . $mid, 2, $mes);
        break;
}

function showLinks()
{
    echo "<a name='new'></a>";

    echo "<div class='pt' style='margin-top:20px;'>" . _MD_NOTE . '</div>';

    echo "<div style='text-align:right'>";

    echo "<a href='#public' class='note_link'>[" . _MD_NOTE_PUB . ']</a> ';

    echo "<a href='#private' class='note_link'>[" . _MD_NOTE_PRI . ']</a> ';

    echo "<a href='#new' class='note_link'>[" . _MD_NOTE_NEW . ']</a> ';

    echo '</div>';
}

function showNotes($mid, $user, $permitted)
{
    global $xoopsDB;

    $myts = MyTextSanitizer::getInstance();

    for ($i = 0; $i < 2; $i++) {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_memo');

        if (0 == $i) {
            echo "<a name='public'></a>";

            $sql .= " WHERE data_id='" . $mid . "' AND Public_flg='1' ORDER BY R_date DESC";
        } else {
            echo "<a name='private'></a>";

            $sql .= " WHERE data_id='" . $mid . "' AND Public_flg='0' AND R_usr='" . $user . "' ORDER BY R_date DESC";
        }

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res)) {
            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                $c = $myts->displayTarea($row['Comment'], 0);

                (0 == $i) ? $p = _MD_NOTE_PUB : $p = _MD_NOTE_PRI;

                echo "<div class='note_info'>" . $row['R_usr'] . ' (' . $row['R_date'] . ', ' . $p . ')</div>';

                echo "<table class='note'>";

                echo "<tr class='content'><td>" . $c . '</td></tr>';

                //uploaded file

                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');

                $sql .= " WHERE tmp1='upfile_name_for_memo' AND tmp3 like '" . $row['id'] . ".%'";

                $res2 = $xoopsDB->query($sql);

                if ($xoopsDB->getRowsNum($res2)) {
                    $row2 = $xoopsDB->fetchArray($res2);

                    $up_name = MOD_PATH . '/' . UPDIR . '/' . $row2['tmp3'];

                    $suf = explode('.', $row2['tmp3']);

                    $suf = $suf[count($suf) - 1];

                    $perm = explode('|', $permitted);

                    for ($j = 0, $jMax = count($perm); $j < $jMax; $j++) {
                        $perm[$j] = mb_strtoupper($perm[$j]);
                    }

                    if (in_array(mb_strtoupper($suf), $perm, true)) {
                        if (file_exists($up_name)) {
                            echo '<tr><td>';

                            echo "<br><a href='" . MOD_URL . '/' . UPDIR . '/' . $row2['tmp3'] . "' target='_blank' class='note_info'>" . $row2['tmp3'] . '</a></td></tr>';
                        }
                    }
                }

                echo '</table>';

                echo "<div class='note_edit'>";

                if ($row['R_usr'] == $user) {
                    echo "<a href='note.php?nid=" . $row['id'] . "&mode=edit' style='text-decoration:none'>[" . _MD_NOTE_EDIT_C . ']</a> ';

                    echo "<a href='note.php?nid=" . $row['id'] . "&mode=delete' style='text-decoration:none'>[" . _MD_NOTE_EDIT_D . ']</a>';
                }

                echo '</div>';
            }
        }
    }
}

function delAttache($nid, $mod_path, $updir)
{
    global $xoopsDB;

    $up_mes = '';

    $up_name = $nid . '.%';

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');

    $sql .= " WHERE tmp1='upfile_name_for_memo' AND tmp3 like '" . $up_name . "'";

    $res = $xoopsDB->query($sql);

    if ($xoopsDB->getRowsNum($res)) {
        $row = $xoopsDB->fetchArray($res);

        $up_name = $mod_path . '/' . $updir . '/' . $row['tmp3'];

        if (file_exists($up_name)) {
            unlink($up_name);

            $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_tmp');

            $sql .= " WHERE tmp1='upfile_name_for_memo' AND tmp3 like '" . $row['tmp3'] . "'";

            $res = $xoopsDB->query($sql);

            $up_mes = '<br>' . _MD_NOTE_ATTACHED_DELETED;
        }
    }

    return $up_mes;
}
