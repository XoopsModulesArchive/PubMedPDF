<?php
declare(strict_types=1);

require __DIR__ . '/nheader.php';

## without pmid registration
if (isset($_POST['upload'])) {
    require __DIR__ . '/include/db.php';

    require __DIR__ . '/include/xml.php';

    require __DIR__ . '/include/news.php';

    $return_url = MOD_URL . '/register_wopmid.php';

    require __DIR__ . '/include/register_wopmid.inc.php';

    ## Check registration

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Custom_t1='" . $data['c_t1'] . "'";

    $res = $xoopsDB->query($sql);

    if ($xoopsDB->getRowsNum($res)) {
        redirect_header(MOD_URL, 2, 'This content is already registered.');
    }

    $seq_no = DB($data, $user);

    GENERATE_XML($data);

    if ($xoopsModuleConfig['news']) {
        NEWS_REGISTER('Reg.wo.PMID', $data['t'], $data['ab'], $user);
    }

    # ID number -> PMID for original content

    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_id') . " SET PMID='-" . $seq_no . "' WHERE id='" . $seq_no . "'";

    $res = $xoopsDB->query($sql);

    $mes = "<div class='pt'>" . _MD_REG_RESULT . '</div>';

    $mes .= '<table>';

    $mes .= "<tr><td class='head' style='width:100px'>File name</td><td class='even'>" . htmlspecialchars($wopmid_id, ENT_QUOTES | ENT_HTML5) . '.xml</td></tr>';

    $mes .= "<tr><td class='head'>Title</td><td class='even'>" . htmlspecialchars($data['t'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';

    $mes .= "<tr><td class='head'>Author(s)</td><td class='even'>" . htmlspecialchars($data['a'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';

    $mes .= "<tr><td class='head'>Journal/Book</td><td class='even'>" . htmlspecialchars($data['j'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';

    $mes .= "<tr><td class='head'>Volume</td><td class='even'>" . htmlspecialchars($data['v'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';

    $mes .= "<tr><td class='head'>Pages</td><td class='even'>" . htmlspecialchars($data['p'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';

    $mes .= "<tr><td class='head'>Year</td><td class='even'>" . $data['y'] . '</td></tr>';

    $mes .= "<tr><td class='head'>Abstract</td><td class='even'>" . htmlspecialchars($data['ab'], ENT_QUOTES | ENT_HTML5) . '</td></tr>';

    $mes .= '</table><br>';

    # folder for uploaded file

    if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
        $file_name = $_FILES['userfile']['name'];

        if (preg_match("/.*(\.pdf)$/i", $file_name)) {
            $file_name = $wopmid_id . '.pdf';

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], WOPDFDIR . '/' . $file_name)) {
                $mes .= $file_name . ' was uploaded.<br><br>';
            } else {
                $mes .= 'File upload was failed.<br><br>';
            }
        } else {
            $mes .= "<b>Error:</b><br>Extension of filename should be '.pdf'.<br>File upload was failed.<br><br>";
        }
    }

    require XOOPS_ROOT_PATH . '/header.php';

    require __DIR__ . '/style.php';

    echo $mes . "<a href='index.php'>HOME</a>";

    require XOOPS_ROOT_PATH . '/footer.php';
} else {
    require XOOPS_ROOT_PATH . '/header.php';

    require __DIR__ . '/style.php';

    echo "<div class='pt'>" . _MD_WO_A . '</div>';

    echo "<form enctype='multipart/form-data' action='' method='POST'>";

    echo "<div class='pc'><table>";

    $mode = 'new';

    require __DIR__ . '/include/regform_wopmid.inc.php';

    // PDF file

    echo '<tr><td><b>Upload</b></td>';

    echo "<td><input type='file' name='userfile'></td>";

    echo '</tr>';

    // Submit

    echo '<tr><td> </td><td>';

    echo "<input type='submit' name='upload' value='submit'>";

    echo '</td></tr>';

    echo '</table></div></form>';

    // for automatic registration

    if ($isadmin) {
        echo "<form enctype='multipart/form-data' action='autorun_wopmid.php' method='POST'>";

        echo "<div class='pt'>" . _MD_REGISTER_AUTO . '</div>';

        echo "<div class='pc'>";

        echo _MD_REGISTER_AUTOINFO . '<br>';

        echo "<input type='submit' name='auto_wopmid' value='submit'>";

        echo '</div>';

        echo '</form>';
    }

    //XML archive download

    echo "<div class='pt'>" . _MD_XML_DOWNLOAD . '</div>';

    echo "<div class='pc'>" . _MD_XML_DOWNLOAD_DESC . '<br>';

    echo "<a href='downloader.php?dir=wo'>[Download]</a></div>";

    require XOOPS_ROOT_PATH . '/footer.php';
}
