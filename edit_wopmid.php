<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/class/description.php';
require __DIR__ . '/include/db.php';

$desc = new description();

require XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/style.php';

if (!isset($_GET['id'])) {
    redirect_header(MOD_URL, 2, _MD_WRONGACCESS);
} else {
    $id = (int)$_GET['id'];

    if (!$data = $desc->getPaperInfo($id)) {
        redirect_header(MOD_URL, 2, _MD_DOSENTEXIST);
    }

    if ($data['pmid'] > 0) {
        redirect_header(MOD_URL, 2, _MD_WRONGACCESS);
    }
}

if (isset($_POST['method'])) {
    require __DIR__ . '/include/xml.php';

    require __DIR__ . '/include/rm.php';

    switch ($_POST['method']) {
        # modify
        case 'submit':
            $mes = _MD_DATA_EDITERROR;

            $old_data = $data;
            $data = [];

            $return_url = MOD_URL . '/edit_wopmid.php?id=' . $id;
            require __DIR__ . '/include/register_wopmid.inc.php';

            if ($data['c_t1'] == $old_data['c_t1']) {
                $mes = _MD_CONFLICT;
            } else {
                if (MOD_DB($id, $data)) {
                    $mes = _MD_EDITED;

                    if (file_exists(MOD_PATH . '/' . UPXML . '/' . $old_data['c_t1'] . '.xml')) {
                        if (!unlink(MOD_PATH . '/' . UPXML . '/' . $old_data['c_t1'] . '.xml')) {
                            $mes .= _MD_DELXMLERROR;
                        }
                    }

                    GENERATE_XML($data);

                    $mes .= _MD_GENERATEXML;

                    if (file_exists(WOPDFDIR . '/' . $old_data['c_t1'] . '.pdf')) {
                        if (!rename(WOPDFDIR . '/' . $old_data['c_t1'] . '.pdf', WOPDFDIR . '/' . $data['c_t1'] . '.pdf')) {
                            $mes .= _MD_RENAMEPDFERROR;
                        } else {
                            $mes .= _MD_RENAMEPDF;
                        }
                    }

                    if (rmJournal($old_data['journal_id'])) {
                        $mes .= $old_data['journal'] . ' ' . _MD_DATA_DELETED . '<br>';
                    }

                    if ($a = rmAuthor($old_data['author_id'])) {
                        $mes .= $a . ' ' . _MD_DATA_DELETED . '<br>';
                    }
                }
            }
            redirect_header(MOD_URL . '/paper_desc.php?id=' . $id, 3, $mes);
            break;
        # upload
        case 'upload':
            $mes = _MD_UPLOADERROR;
            if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                $file_name = $_FILES['userfile']['name'];

                if (preg_match("/.*(\.pdf)$/i", $file_name)) {
                    if (move_uploaded_file($_FILES['userfile']['tmp_name'], WOPDFDIR . '/' . $data['c_t1'] . '.pdf')) {
                        $mes = _MD_UPLOADED;
                    }
                } else {
                    $mes = _MD_UPLOADERROR2;
                }
            }
            break;
        # remove
        case 'remove':
            $mes = _MD_DATA_DELETEDERROR;
            if ($data['reg_usr'] == $user || $isadmin) {
                $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE id='" . $id . "'";

                $res = $xoopsDB->query($sql);

                if ($res) {
                    $mes = _MD_DEL_DATA;

                    if (rmJournal($data['journal_id'])) {
                        $mes .= $data['journal'] . ' ' . _MD_DATA_DELETED . '<br>';
                    }

                    if ($a = rmAuthor($data['author_id'])) {
                        $mes .= $a . ' ' . _MD_DATA_DELETED . '<br>';
                    }

                    if (file_exists(WOPDFDIR . '/' . $data['c_t1'] . '.pdf')) {
                        if (unlink(WOPDFDIR . '/' . $data['c_t1'] . '.pdf')) {
                            $mes .= _MD_DEL_PDF;
                        } else {
                            $mes .= _MD_DEL_PDF2;
                        }
                    }

                    if (file_exists(MOD_PATH . '/' . UPXML . '/' . $data['c_t1'] . '.xml')) {
                        if (unlink(MOD_PATH . '/' . UPXML . '/' . $data['c_t1'] . '.xml')) {
                            $mes .= _MD_DEL_XML;
                        } else {
                            $mes .= _MD_DEL_XML2;
                        }
                    }
                }
            } else {
                $mes = _MD_WRONGACCESS;
            }
            break;
        default:
            $mes = _MD_WRONGACCESS;
    }

    redirect_header(MOD_URL, 3, $mes);
}

echo "<div class='pt'>" . _MD_EDIT_INFO . '</div>';
echo "<form enctype='multipart/form-data' action='?id=" . $id . "' method='POST'>";
echo "<div class='pc'><table>";

$mode = 'edit';
require __DIR__ . '/include/regform_wopmid.inc.php';

echo "<tr><td> </td><td><input type='submit' name='method' value='submit'></td></tr>";
echo '</table></div>';
echo '</form>';

// PDF file upload form
echo "<div class='pt'>" . _MD_UPLOAD_INFO . '/' . _MD_CHANGEPDF . '</div>';
echo "<form enctype='multipart/form-data' action='?id=" . $id . "' method='POST'>";
echo "<div class='pc'>";
echo 'Upload File &nbsp;&nbsp;';
echo "<input type='file' name='userfile'>";
echo "<input type='submit' name='method' value='upload'>";
echo '</div>';
echo '</form>';

// Remove form
echo "<div class='pt'>" . _MD_REMOVE_INFO . '</div>';
echo "<form action='?id=" . $id . "' method='POST'>";
echo "<input type='submit' name='method' value='remove'>";
echo '</form>';

echo "<br><br><a href='" . MOD_URL . "'>HOME</a>";
require XOOPS_ROOT_PATH . '/footer.php';
