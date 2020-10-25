<?php

echo '<b>' . _PA_CHECKXML . '</b><br>';
$deleted = '';
$undeleted = '';
$d = MOD_PATH . '/' . UPXML;
if ($handle = opendir($d)) {
    while (false !== $file = readdir($handle)) {
        if ('.' != $file && '..' != $file) {
            if (preg_match("/.*(\.xml)$/i", $file)) {
                $f = str_replace('.xml', '', $file);

                $sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . " where Custom_t1='" . $f . "'";

                $rs = $xoopsDB->query($sql);

                if (!$xoopsDB->getRowsNum($rs)) {
                    if (unlink($d . '/' . $file)) {
                        $deleted .= $file . '<br>';
                    } else {
                        $undeleted .= "<span style='color:red'>" . _PA_FALSE . '</span> ' . $file . '<br>';
                    }
                }
            } else {
                if (unlink($d . '/' . $file)) {
                    $deleted .= $file . '<br>';
                } else {
                    $undeleted .= "<span style='color:red'>" . _PA_FALSE . '</span> ' . $file . '<br>';
                }
            }
        }
    }

    closedir($handle);
}

if (!empty($deleted)) {
    echo '<u>' . _PA_DELETEDXML . '</u><br>';

    echo $deleted . '<br>';
}

if (!empty($undeleted)) {
    echo $undeleted . '<br>';
}

if (empty($deleted) && empty($undeleted)) {
    echo _PA_NOPROBLEM . '<br><br>';
}

$make = '';
echo '<b>' . _PA_CHECKXML . '2</b><br>';
$rs = $xoopsDB->query('select * from ' . $xoopsDB->prefix('pmid_id') . ' where pmid<0');
while (false !== ($row = $xoopsDB->fetchArray($rs))) {
    if (!file_exists(MOD_PATH . '/' . UPXML . '/' . $row['Custom_t1'] . '.xml')) {
        $data = [];

        $data['t'] = $row['Title'];

        $data['t_jp'] = $row['Title_JP'];

        $data['y'] = $row['Year'];

        $data['a_jp'] = $row['Author_JP'];

        $data['j'] = $row['Journal'];

        $data['v'] = $row['Volume'];

        $data['p'] = $row['Page'];

        $data['ab'] = $row['Abstract'];

        $data['c_t1'] = $row['Custom_t1'];

        $data['a'] = '';

        $a = explode(',', mb_substr($row['Author'], 0, -1));

        for ($k = 0, $kMax = count($a); $k < $kMax; $k++) {
            $a[$k] = str_replace('[', '', $a[$k]);

            $a[$k] = str_replace(']', '', $a[$k]);

            $sql = 'SELECT Author FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE id='" . $a[$k] . "'";

            $res2 = $xoopsDB->query($sql);

            $row2 = $xoopsDB->fetchArray($res2);

            $data['a'] .= $row2['Author'] . ', ';
        }

        $data['a'] = mb_substr($data['a'], 0, -2);

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $data['j'] . "'";

        $res2 = $xoopsDB->query($sql);

        $row2 = $xoopsDB->fetchArray($res2);

        $data['j'] = $row2['Journal'];

        (1 == $row2['Book']) ? $data['jb'] = 'b' : $data['jb'] = 'j';

        foreach ($data as $key => $value) {
            $data[$key] = str_replace('"', '&#34;', $data[$key]);
        }

        GENERATE_XML($data);

        $make .= $row['Custom_t1'] . '.xml<br>';
    }
}

if (!empty($make)) {
    echo '<u>' . _PA_GENERATEDXML . '</u><br>';

    echo $make . '<br>';
} else {
    echo _PA_NOPROBLEM . '<br><br>';
}

echo '<b>' . _PA_CHECKPDF . '</b><br>';
$pdfs = '';
$nonpdf = '';
if ($handle = opendir(WOPDFDIR)) {
    while (false !== $file = readdir($handle)) {
        if ('.' != $file && '..' != $file) {
            if (preg_match("/.*(\.pdf)$/i", $file)) {
                $f = str_replace('.pdf', '', $file);

                $sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . " where Custom_t1='" . $f . "'";

                $rs = $xoopsDB->query($sql);

                if (!$xoopsDB->getRowsNum($rs)) {
                    $pdfs .= "[<a href='index.php?mode=pdfdel&pdf=" . $file . "'>" . _PA_DELETE . '</a>] ';

                    $pdfs .= "<a href='" . MOD_URL . '/' . PDFDIR2 . '/wopmid/' . $file . "'>" . $file . '</a><br>';
                }
            } else {
                if (unlink(WOPDFDIR . '/' . $file)) {
                    $nonpdf .= $file . '<br>';
                }
            }
        }
    }

    closedir($handle);
}

if (!empty($pdfs)) {
    echo '<u>' . _PA_NOTICEPDF . '</u><br>';

    echo $pdfs . '<br>';
} else {
    echo _PA_NOPROBLEM . '<br>';
}

if (!empty($nonpdf)) {
    echo '<u>' . _PA_DELTEDUNKNOWN . '</u><br>';

    echo $nonpdf . '<br>';
}
