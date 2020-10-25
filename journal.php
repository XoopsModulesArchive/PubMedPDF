<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/class/description.php';
require XOOPS_ROOT_PATH . '/header.php';
$desc = new description($user);

if (isset($_GET['make_s'])) {
    require __DIR__ . '/class/functions.php';

    $func = new functions($user);

    $p = $func->makeShortcut($_GET['pass'], $_GET['name']);

    redirect_header(MOD_URL . '/' . $p, 1, _MD_SCUTMADE);
}

$id = (int)$_GET['journal'];
$sql = 'select * from ' . $xoopsDB->prefix('pmid_journal') . " where id='" . $id . "'";
$res = $xoopsDB->query($sql);
$row = $xoopsDB->fetchArray($res);
$journal = $row['Journal'];

### HOME > Journal ###
if (!isset($_GET['year'])) {
    # navibar

    $name = $journal;

    if (mb_strlen($name) > 18) {
        $name = mb_substr($name, 0, 18) . '...';
    }

    $pass = 'journal.php~~journal=' . $id;

    $navi = "[<a href='journal.php?make_s=y&pass=" . $pass . '&name=' . $name . "'>Shortcut</a>]";

    echo '<table style="width:100%">';

    echo '<tr><td style="text-align:right">' . $navi . '</td></tr>';

    echo '<tr><td><a href="index.php">HOME</a> > ' . $journal . '</td></tr>';

    echo '</table><hr>';

    # get journal's items

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Journal='" . $id . "' ORDER BY Year DESC";

    $res = $xoopsDB->query($sql);

    for ($i = 0; ; $i++) {
        if (!$row = $xoopsDB->fetchArray($res)) {
            break;
        }

        if (!isset($year[$row['Year']])) {
            $year[$row['Year']] = 0;
        }

        $year[$row['Year']]++;

        $item[$i] = $row['id'];
    }

    # year bar

    foreach ($year as $key => $value) {
        echo '<a href="journal.php?journal=' . $id . '&year=' . $key . '">' . $key . '</a> (' . $value . ') &nbsp;';
    }

    echo '<hr>';

    echo $desc->getPaper($item, PDFDIR2);

### HOME > Journal > Year ###
} elseif (isset($_GET['year'])) {
    $year = (int)$_GET['year'];

    # navibar

    $name = $year . '_' . $journal;

    if (mb_strlen($name) > 18) {
        $name = mb_substr($name, 0, 18) . '...';
    }

    $pass = 'journal.php~~journal=' . $id . '^^year=' . $year;

    $navi = "[<a href='journal.php?make_s=y&pass=" . $pass . '&name=' . $name . "'>Shortcut</a>]";

    echo '<table style="width:100%">';

    echo '<tr><td style="text-align:right">' . $navi . '</td></tr>';

    echo '<tr><td><a href="index.php">HOME</a> > <a href="journal.php?journal=' . $id . '">' . $journal . '</a> > ' . $year . '</td></tr>';

    echo '</table><hr>';

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Journal='" . $id . "' AND Year='" . $year . "'";

    $res = $xoopsDB->query($sql);

    for ($i = 0; ; $i++) {
        if (!$row = $xoopsDB->fetchArray($res)) {
            break;
        }

        $item[$i] = $row['id'];
    }

    echo $desc->getPaper($item, PDFDIR2);
}

require XOOPS_ROOT_PATH . '/footer.php';
