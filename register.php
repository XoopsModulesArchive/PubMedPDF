<?php

require __DIR__ . '/nheader.php';

$mode = 'default';
if (isset($_GET['showlist'])) {
    $mode = 'list';
} elseif (isset($_POST['auto_run2'])) {
    $mode = 'autorun';
}

switch ($mode) {
    case 'list':
        $how = $_GET['how'];
        $data = '';

        $sql = 'SELECT PMID FROM ' . $xoopsDB->prefix('pmid_id');
        if (isset($_GET['usr_only'])) {
            $sql .= " WHERE R_usr='" . $user . "'";
        }
        $sql .= ' ORDER BY PMID';
        $res = $xoopsDB->query($sql);

        if ('all' == $how) {
            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                if ($row['PMID'] && $data) {
                    $data .= ',';
                }

                if ($row['PMID']) {
                    $data .= $row['PMID'];
                }
            }

            #PMID with PDF
        } elseif ('have' == $how) {
            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                if (file_exists(PDFDIR . '/' . $row['PMID'] . '.pdf')) {
                    if ($row['PMID'] && $data) {
                        $data .= ',';
                    }

                    if ($row['PMID']) {
                        $data .= $row['PMID'];
                    }
                }
            }

            #PMID without PDF
        } elseif ('havenot' == $how) {
            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                if (!file_exists(PDFDIR . '/' . $row['PMID'] . '.pdf')) {
                    if ($row['PMID'] && $data) {
                        $data .= ',';
                    }

                    if ($row['PMID']) {
                        $data .= $row['PMID'];
                    }
                }
            }
        }

        $fp = fopen('datalist.txt', 'wb');
        fwrite($fp, $data);
        fclose($fp);
        header('Location:' . MOD_URL . '/include/export.php');

        require XOOPS_ROOT_PATH . '/header.php';
        regFrom($isadmin, $user);
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'autorun':
        require XOOPS_ROOT_PATH . '/class/snoopy.php';
        include 'include/pubmed.php';
        include 'include/db.php';

        $mes = '';

        if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
            $file_name = $_FILES['userfile']['name'];

            if (!preg_match("/.*(\.txt)$/i", $file_name)) {
                redirect_header(MOD_URL, 2, _MD_TXTONLY);
            }

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], 'tmp.txt')) {
                $data = '';

                $fp = fopen('tmp.txt', 'rb');

                while (!feof($fp)) {
                    $data .= fgets($fp);
                }

                fclose($fp);

                unlink('tmp.txt');

                $pmid = explode(',', $data);

                # proxy

                if ($xoopsModuleConfig['proxy']) {
                    $proxy = new Snoopy();

                    $proxy->read_timeout = 0;

                    $proxy->proxy_host = $xoopsModuleConfig['proxy_url'];

                    $proxy->proxy_port = $xoopsModuleConfig['proxy_port'];
                } else {
                    $proxy = 0;

                    $snoopy = new Snoopy();
                }

                for ($i = 0, $count = 0, $iMax = count($pmid); $i < $iMax; $i++, $count++) {
                    $pmid[$i] = (int)$pmid[$i];

                    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE PMID='" . $pmid[$i] . "'";

                    $res = $xoopsDB->query($sql);

                    if (!$xoopsDB->getRowsNum($res)) {
                        if ($count > 70) {
                            for ($j = 0; $j < 10000; $j++) {
                            }

                            $count = 0;
                        }

                        $pxml = MOD_PATH . '/' . PXMLDIR;

                        $sxml = MOD_PATH . '/' . SXMLDIR;

                        # use pubmed.xml

                        if (file_exists($pxml . '/' . $pmid[$i] . '.xml')) {
                            $pm_data = PubMedByFile($pmid[$i], $pxml);

                            if ($pm_data) {
                                $mes .= ' - ' . $pm_data['t'] . '<br>';

                                DB($pm_data, $user);
                            }

                            # use search.xml
                        } elseif (file_exists($sxml . '/' . $pmid[$i] . '.xml')) {
                            $pm_data = PubMedByFile($pmid[$i], $sxml);

                            if ($pm_data) {
                                $mes .= ' - ' . $pm_data['t'] . '<br>';

                                DB($pm_data, $user);

                                rename($sxml, $pxml);
                            }

                            # get xml from PubMed
                        } else {
                            if ($pm_data = PubMed($pmid[$i], $proxy, $snoopy, PXMLDIR)) {
                                $mes .= ' - ' . $pm_data['t'] . '<br>';

                                DB($pm_data, $user);
                            }
                        }
                    }
                }
            } else {
                $mes .= 'upload failed.';
            }
        }

        require XOOPS_ROOT_PATH . '/header.php';
        echo $mes;
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'default':
        require XOOPS_ROOT_PATH . '/header.php';
        regForm($isadmin, $user);
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
}

function regForm($isadmin, $user)
{
    require __DIR__ . '/style.php';

    echo "<div class='pt'>" . _MD_PDF_A . '</div>';

    echo "<div class='pc'>";

    echo _MD_PDF_A_DESC;

    echo "<br><br><form enctype='multipart/form-data' action='upload.php' method='POST'>";

    echo "<input type='file' name='userfile'>";

    echo "<input type='submit' name='upload' value='submit'>";

    echo '</form></div>';

    echo "<div class='pt'>" . _MD_PDF_B . '</div>';

    echo "<div class='pc'>";

    echo _MD_PDF_B_DESC;

    echo "<br><br><form action='upload.php' method='POST'>";

    echo "<input type='id' name='pmid'>";

    echo "<input type='submit' name='reg_by_id' value='submit'>";

    echo '</form></div>';

    if ($isadmin) {
        echo "<div class='pt'>" . _MD_PDF_C . '</div>';

        echo "<div class='pc'>";

        echo _MD_PDF_C_DESC;

        echo "<br><br><form action='auto_run.php' method='POST'>";

        echo "<input type='submit' value='submit'>";

        echo '</form></div>';
    }

    echo "<div class='pt'>" . _MD_PDF_D . '</div>';

    echo "<div class='pc'>";

    echo _MD_PDF_D_DESC;

    echo "<br><br><form enctype='multipart/form-data' action='register.php' method='POST'>";

    echo "<input type='file' name='userfile'>";

    echo "<input type='submit' name='auto_run2' value='submit'>";

    echo '</form></div>';

    echo "<div class='pt'>" . _MD_PDF_E . '</div>';

    echo "<div class='pc'>";

    echo _MD_PDF_E_DESC;

    echo "<br><br><form action='register.php' method='GET'>";

    echo "<input type='radio' name='how' value='havenot' checked>" . _MD_L1;

    echo "<input type='radio' name='how' value='have'>" . _MD_L2;

    echo "<input type='radio' name='how' value='all'>" . _MD_L3 . '<br>';

    echo "<input type='checkbox' name='usr_only' value='y'>" . _MD_L4 . '<br><br>';

    echo "<input type='submit' name='showlist' value='download'>";

    echo '</form></div>';

    //XML archive download

    echo "<div class='pt'>" . _MD_XML_DOWNLOAD . '</div>';

    echo "<div class='pc'>" . _MD_XML_DOWNLOAD_DESC . '<br>';

    echo "<a href='downloader.php?dir=w'>[Download]</a></div>";

    if ('guest' != $user) {
        echo "<div class='pt'>" . _MD_PDF_F . '</div>';

        echo "<div class='pc'>";

        echo _MD_PDF_F_DESC;

        echo "<br><br><form action='delete.php' method='POST'>";

        echo "<input type='text' name='id'>";

        echo "<input type='submit' name='upload' value='submit'>";

        echo '</form></div>';
    }
}
