<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/class/description.php';

$br = "\r\n";
$method = $_GET['method'] ?? '';

$tmp_id = 0;
$format = '';
$tempname = '';
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_template') . " WHERE id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    if ($xoopsDB->getRowsNum($res)) {
        $row = $xoopsDB->fetchArray($res);

        if (!mb_strstr($row['name'], $user . '_')) {
            redirect_header(MOD_URL, 2, _MD_WRONGACCESS);
        }

        $tempname = str_replace($user . '_', '', $row['name']);

        $format = $row['template'];

        $tmp_id = $id;
    }
}

switch ($method) {
    # make new template
    case 'make_new':
        require XOOPS_ROOT_PATH . '/header.php';
        require __DIR__ . '/style.php';
        echo "<div class='pt'>" . _MD_BIBLIO_NEW . '</div>';
        getForm('', '[N])[][A]([Y]),[T],[J],[V],pp[P]', 'do_make_new');
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'do_make_new':
        $name = addslashes($user . '_' . $myts->stripSlashesGPC($_GET['tmpl_name']));
        $format = addslashes($myts->stripSlashesGPC($_GET['template']));
        $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_template');
        $sql .= " VALUES('','" . $name . "','" . $format . "')";
        if ($res = $xoopsDB->queryF($sql)) {
            $mes = _MD_BIBLIO_TEMPMADE;
        } else {
            $mes = _MD_BIBLIO_TEMPMADEERROR;
        }
        redirect_header(MOD_URL . '/biblio.php', 2, $mes);
        break;
    # edit template
    case 'edit':
        require XOOPS_ROOT_PATH . '/header.php';
        require __DIR__ . '/style.php';
        echo "<div class='pt'>" . _MD_BIBLIO_MODIFY . '</div>';
        getForm($tempname, $format, 'do_edit', $tmp_id);
        require XOOPS_ROOT_PATH . '/footer.php';
        break;
    case 'do_edit':
        if (isset($_GET['deltemp']) && 'y' == $_GET['deltemp']) {
            $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_template') . " WHERE id='" . $tmp_id . "'";
        } else {
            $name = addslashes($user . '_' . $myts->stripSlashesGPC($_GET['tmpl_name']));

            $format = addslashes($myts->stripSlashesGPC($_GET['template']));

            $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_template');

            $sql .= " SET name='" . $user . '_' . $name . "', template='" . $format . "'";

            $sql .= " WHERE id='" . $tmp_id . "'";
        }
        if ($res = $xoopsDB->queryF($sql)) {
            $mes = _MD_BIBLIO_TEMPEDIT;
        } else {
            $mes = _MD_BIBLIO_TEMPEDITERROR;
        }
        redirect_header(MOD_URL . '/biblio.php', 2, $mes);
        break;
    # download as bibtex
    case 'bibtex':
        $desc = new description();
        $bibtex = '';
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');
        $sql .= " WHERE tmp1='biblio' AND tmp2='" . $user . "'";
        $res = $xoopsDB->query($sql);
        while (false !== ($row = $xoopsDB->fetchArray($res))) {
            $data = $desc->getPaperInfo($row['tmp3']);

            $author = str_replace(', ', ' and ', $data['author']);

            $bibkey = mb_substr($author, 0, mb_strpos($author, ' ')) . $y . ':' . mb_substr($t, 0, 8) . ',' . $br;

            $bibkey = str_replace(' ', '_', $bibkey);

            $bibtex .= 'Article{';

            $bibtex .= $bibkey;

            $bibtex .= "  author =\t{" . $author . '},' . $br;

            $bibtex .= "  title =\t{" . $data['title'] . '},' . $br;

            $bibtex .= "  journal =\t{" . $data['journal'] . '},' . $br;

            $bibtex .= "  year = \t" . $data['year'] . ',' . $br;

            $bibtex .= "  volume = \t" . $data['v'] . ',' . $br;

            $bibtex .= "  pages = \t{" . str_replace('-', '--', $data['pp']) . '}' . $br;

            $bibtex .= '}' . $br;
        }
        $fp = fopen('datalist.txt', 'wb');
        fwrite($fp, $bibtex);
        fclose($fp);
        header('Location:' . MOD_URL . '/include/export.php');
        break;
    # download text
    case 'text':
        $desc = new description();
        $i = 0;
        $text = '';
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');
        $sql .= " WHERE tmp1='biblio' AND tmp2='" . $user . "'";
        $res = $xoopsDB->query($sql);
        while (false !== ($row = $xoopsDB->fetchArray($res))) {
            $i++;

            $text .= useFormat($desc->getPaperInfo($row['tmp3']), $i, $format, 'text');

            $text .= $br;
        }
        $fp = fopen('datalist.txt', 'wb');
        fwrite($fp, $text);
        fclose($fp);
        header('Location:' . MOD_URL . '/include/export.php');
        break;
    # delete item
    case 'delete':
        foreach ($_POST['item'] as $item) {
            $item = (int)$item;

            $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_tmp');

            $sql .= " WHERE tmp1='biblio' AND tmp2='" . $user . "' AND tmp3='" . $item . "'";

            $res = $xoopsDB->query($sql);
        }
        header('Location:' . MOD_URL . '/biblio.php?id=' . $tmp_id);
        break;
    # top page
    default:
        require XOOPS_ROOT_PATH . '/header.php';
        require __DIR__ . '/style.php';

        echo <<<E
		<script>
		function checkFData(pref, flg){
			if (! document.getElementById) return;
			for (i=1;; i++) {
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

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');
        $sql .= " WHERE tmp1='biblio' AND tmp2='" . $user . "'";
        $res = $xoopsDB->query($sql);
        $num = $xoopsDB->getRowsNum($res);

        echo "<div class='pt'>" . _MD_BIBLIO . '</div>';
        echo "<div style='margin-left:20px;'>'" . $num . "' " . _MD_BIBLIO_EXIST . '</div>';

        # select template
        echo "<div class='pt' style='margin-top:20px'>" . _MD_BIBLIO_SELECT . '</div>';
        echo "<div style='text-align:right'><a href='biblio.php?method=make_new'>" . _MD_BIBLIO_MAKENEW . '</a></div>';

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_template');
        $sql .= " WHERE name like '" . $user . "_%'";
        $res = $xoopsDB->query($sql);
        if ($xoopsDB->getRowsNum($res)) {
            $i = 0;

            echo '<table><tr>';

            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                if (!($i % 3)) {
                    echo '</tr><tr>';
                }

                $name = str_replace($user . '_', '', $row['name']);

                echo "<td style='width:33%'>[<a href='biblio.php?id=" . $row['id'] . "&method=edit'>edit</a>] ";

                echo "<a href='biblio.php?id=" . $row['id'] . "'>" . htmlspecialchars($name, ENT_QUOTES | ENT_HTML5) . '</a></td>';

                $i++;
            }

            echo '</tr></table>';
        }

        # show data
        if (!empty($format)) {
            echo "<div class='pt' style='margin-top:20px'>" . _MD_BIBLIO_SHOW . '</div>';

            echo "<div style='text-align:right'>[" . _MD_BIBLIO_SHOW . 'DL] ';

            echo "<a href='biblio.php?id=" . $tmp_id . "&method=text'>Text</a> / ";

            echo "<a href='biblio.php?method=bibtex'>BiBTeX</a>";

            echo '</div>';

            echo "<center><form method='POST' action='?id=" . $tmp_id . "&method=delete'>";

            echo "<table style='width:90%; text-align:left'>";

            echo "<tr><td colspan='2'>";

            echo "<a href=\"javascript:checkFData('data', 1)\">" . _MD_CHECK_ALL . '</a> / ';

            echo "<a href=\"javascript:checkFData('data', 0)\">" . _MD_FAVORITE_FREE . '</a><br><br>';

            echo '</td></tr>';

            $desc = new description();

            $i = 0;

            $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');

            $sql .= " WHERE tmp1='biblio' AND tmp2='" . $user . "'";

            $res = $xoopsDB->query($sql);

            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                $i++;

                echo "<tr><td><input id='data" . $i . "' type='checkbox' name='item[]' value='" . $row['tmp3'] . "'></td><td>";

                echo useFormat($desc->getPaperInfo($row['tmp3']), $i, $format, 'list');

                echo '</td></tr>';
            }

            echo '</table>';

            echo '<br><br>';

            echo "<input type='submit' value='delete checked item'>";

            echo '</form></center>';
        }
        require XOOPS_ROOT_PATH . '/footer.php';
}

function useFormat($data, $i, $format, $type)
{
    $format = str_replace('[N]', $i, $format);

    $format = str_replace('[A]', $data['author'], $format);

    $format = str_replace('[T]', $data['title'], $format);

    $format = str_replace('[Y]', $data['year'], $format);

    $format = str_replace('[V]', $data['v'], $format);

    $format = str_replace('[P]', $data['pp'], $format);

    $format = str_replace('[J]', $data['journal'], $format);

    $format = str_replace('[PMID]', $data['pmid'], $format);

    if ('list' == $type) {
        $format = str_replace('[]', '&nbsp;', $format);
    } else {
        $format = str_replace('[]', ' ', $format);
    }

    return $format;
}

function getForm($name, $format, $mt, $id = 0)
{
    echo "<center><form action='biblio.php' method='GET'>";

    echo "<table class='outer' style='width:95%; text-align:left'>";

    echo "<tr><td class='head'>Format</td><td class='even'>";

    echo '<table><tr><td>[N] number</td><td>[A] authors</td><td>[T] title</td><td>[J] journal</td></tr>';

    echo '<tr><td>[Y] year</td><td>[V] volume</td><td>[P] page</td><td>[PMID] pmid</td></tr>';

    echo "<tr><td>[] space </td><td colspan='3'></td></tr></table></td></tr>";

    echo "<tr><td style='width:60px' class='head'>Name</td>";

    echo "<td class='even'><input type='text' name='tmpl_name' value='" . $name . "' style='width:50%'></td></tr>";

    echo "<tr><td class='head'>Template</td><td class='even'>";

    echo "<textarea name='template' rows='3' style='width:99%'>" . $format . '</textarea><br>';

    echo '</td></tr>';

    if ($id) {
        echo "<tr><td class='head'>Delete</td><td class='even'>";

        echo "<input type='checkbox' name='deltemp' value='y'>";

        echo ' ' . _MD_DELTEMPLATE . '</td></tr>';
    }

    echo "<tr><td class='head'></td><td class='even'>";

    echo "<input type='submit' value='submit' name='new_tmpl'></td></tr></table>";

    echo "<input type='hidden' name='method' value='" . $mt . "'>";

    if ($id) {
        echo "<input type='hidden' name='id' value='" . $id . "'>";
    }

    echo '</form></center>';
}
