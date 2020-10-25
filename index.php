<?php

require __DIR__ . '/nheader.php';
require XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/style.php';

$navi = '<a href="http://www.web-learner.com/" target="_blank">[Help]</a> ';
$navi .= '<a href="http://www.ncbi.nlm.nih.gov/entrez/query.fcgi" target="_blank">[PubMed]</a> ';

### Shortcut delete ###
if (isset($_GET['scutdel'])) {
    $id = (int)$_GET['scutdel'];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_shortcut') . " WHERE id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    if ($xoopsDB->getRowsNum($res) > 0) {
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_shortcut') . " WHERE id='" . $id . "'";

        $res = $xoopsDB->queryF($sql);
    }

    redirect_header(MOD_URL, 1, _MD_SCUTDEL);
}

$res = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('pmid_id'));
echo "<table style='width:100%; margin-bottom:20px'><tr>";
echo '<td><b>' . $xoopsDB->getRowsNum($res) . '</b> papers registered</td>';
echo "<td style='text-align:right'>" . $navi . '</td>';
echo '</tr></table>';

### Shortcut ###
if ('guest' != $user) {
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_shortcut');

    $sql .= " WHERE Usr='" . $user . "' ORDER BY Target";

    $res = $xoopsDB->query($sql);

    if ($xoopsDB->getRowsNum($res) > 0) {
        echo "<div class='pt'>" . _MD_SCUT . '</div>';

        echo "<table style='width:100%'><tr>";

        $i = 0;

        while (false !== ($row = $xoopsDB->fetchArray($res))) {
            $pass = XOOPS_URL . '/modules/PubMedPDF/' . $row['Target'];

            $url = "[<a href='index.php?scutdel=" . $row['id'] . "'><img src='images/delete.png'></a>] ";

            $url .= "<a href='" . $pass . "'>" . $row['Name'] . '</a>';

            if (!($i % 3)) {
                echo '</tr><tr>';
            }

            echo "<td style='width:33%'>" . $url . '</td>';

            $i++;
        }

        echo '</tr></table><br>';
    }
}

### Author ###
echo "<div class='pt'>" . _MD_AUTHOR . '</div>';
for ($i = 'A', $j = 0; $j < 26; $i++, $j++) {
    $sql = 'SELECT id FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE Author LIKE '" . $i . "%'ORDER BY Author";

    $res = $xoopsDB->query($sql);

    if ($xoopsDB->getRowsNum($res)) {
        echo "<a href='author.php?initial=" . $i . "'>" . $i . '</a> &nbsp;';
    }
}
echo '<br><br>';

### Year ###
$year = [];
echo "<div class='pt'>" . _MD_YEAR . '</div>';
$sql = 'SELECT Year FROM ' . $xoopsDB->prefix('pmid_id') . ' ORDER BY Year';
$res = $xoopsDB->query($sql);
while (false !== ($row = $xoopsDB->fetchArray($res))) {
    if (!isset($year[$row['Year']])) {
        $year[$row['Year']] = 0;
    }

    $year[$row['Year']]++;
}
echo '<table><tr>';
$i = 0;
foreach ($year as $key => $value) {
    if (!($i % 8)) {
        echo '</tr><tr>';
    }

    echo "<td><a href='year.php?year=" . $key . "'>" . $key . '</a> (' . $value . ') &nbsp;</td>';

    $i++;
}
echo '</tr></table><br>';

### Journal ###
echo "<div class='pt'>" . _MD_JB . '</div>';

#journal initial
$link = [];
$sql = 'SELECT Journal FROM ' . $xoopsDB->prefix('pmid_journal') . ' ORDER BY Journal';
$res = $xoopsDB->query($sql);
while (false !== ($row = $xoopsDB->fetchArray($res))) {
    if ('(unknown)' == $row['Journal']) {
        continue;
    }

    $i = mb_strtoupper(mb_substr($row['Journal'], 0, 1));

    if (!in_array($i, $link, true)) {
        $link[] = $i;
    }
}
for ($i = 0, $iMax = count($link); $i < $iMax; $i++) {
    echo "<a href='#" . $link[$i] . "'>" . $link[$i] . '</a>&nbsp;&nbsp;';
}
echo '<br><br>';

$sql = 'SELECT id, Journal, Book FROM ' . $xoopsDB->prefix('pmid_journal') . ' ORDER BY Journal';
$res = $xoopsDB->query($sql);
$j = [];
$index = [];
while (false !== ($row = $xoopsDB->fetchArray($res))) {
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Journal='" . $row['id'] . "'";

    $res2 = $xoopsDB->query($sql);

    (mb_strlen($row['Journal']) > 18) ? $J = mb_substr($row['Journal'], 0, 18) . '...' : $J = $row['Journal'];

    if ($xoopsDB->getRowsNum($res2)) {
        $index[] = mb_strtoupper(mb_substr($J, 0, 1));

        if ($row['Book']) {
            $J = '<i>' . $J . '</i>';
        }

        $j[] = "<a href='journal.php?journal=" . $row['id'] . "'>" . $J . '</a> (' . $xoopsDB->getRowsNum($res2) . ')';
    }
}

# Journal method
$pre_index = '';
echo '<table><tr>';
for ($i = 0, $td = 0, $iMax = count($j); $i < $iMax; $i++, $td++) {
    if ($index[$i] != $pre_index) {
        if (0 == $td) {
            if ('(' != $index[$i]) {
                echo "</tr><tr><td colspan='3' class='even'>";

                echo "<a name='" . $index[$i] . "'></a>" . $index[$i] . '</td></tr><tr>';
            }
        } elseif (1 == $td) {
            echo '<td> </td><td> </td>';

            echo "</tr><tr><td colspan='3' class='even'>";

            echo "<a name='" . $index[$i] . "'></a>" . $index[$i] . '</td></tr><tr>';

            $td = 0;
        } elseif ((2 == $td)) {
            echo '<td> </td>';

            echo "</tr><tr><td colspan='3' class='even'>";

            echo "<a name='" . $index[$i] . "'></a>" . $index[$i] . '</td></tr><tr>';

            $td = 0;
        } elseif (3 == $td) {
            echo "</tr><tr><td colspan='3' class='even'>";

            echo "<a name='" . $index[$i] . "'></a>" . $index[$i] . '</td></tr><tr>';

            $td = 0;
        }
    } else {
        if (3 == $td) {
            echo '</tr><tr>';

            $td = 0;
        }
    }

    echo "<td style='width:33%'>" . $j[$i] . '</td>';

    $pre_index = $index[$i];
}
echo '</tr></table>';

require XOOPS_ROOT_PATH . '/footer.php';
