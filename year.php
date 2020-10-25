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

$year = (int)$_GET['year'];
if (!$year) {
    $year = '0000';
}

### HOME > Year ###
if (!isset($_GET['journal'])) {
    # navibar

    $navi = "[<a href='year.php?make_s=y&pass=year.php~~year=" . $year . '&name=' . $year . "'>Shortcut</a>]";

    echo '<table style="width:100%">';

    echo '<tr><td style="text-align:right">' . $navi . '</td></tr>';

    echo '<tr><td><a href="index.php">HOME</a> > ' . $year . '</td></tr>';

    echo '</table><hr>';

    # get year's items

    $journal = [];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Year='" . $year . "'";

    $res = $xoopsDB->query($sql);

    for ($i = 0; ; $i++) {
        if (!$row = $xoopsDB->fetchArray($res)) {
            break;
        }

        if (!isset($journal[$row['Journal']])) {
            $journal[$row['Journal']] = 0;
        }

        $journal[$row['Journal']]++;

        $item[$i] = $row['id'];
    }

    # journal bar

    $journal2 = [];

    foreach ($journal as $key => $value) {
        $sql = 'SELECT Journal,Book FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $key . "'";

        $res = $xoopsDB->query($sql);

        $row = $xoopsDB->fetchArray($res);

        $journal2[] = $row['Journal'] . '??' . $key . '??' . $value . '??' . $row['Book'];
    }

    sort($journal2);

    echo '<table><tr>';

    for ($i = 0, $iMax = count($journal2); $i < $iMax; $i++) {
        if (!($i % 2)) {
            echo '</tr><tr>';
        }

        $j = explode('??', $journal2[$i]);

        if (mb_strlen($j[0]) > 18) {
            $j[0] = mb_substr($j[0], 0, 18) . '...';
        }

        if ($j[3]) {
            $j[0] = '<i>' . $j[0] . '</i>';
        }

        echo '<td><a href="year.php?year=' . $year . '&journal=' . $j[1] . '">' . $j[0] . '</a> (' . $j[2] . ')</td>';
    }

    echo '</tr></table><hr>';

#echo $desc->getPaper($item, PDFDIR2);

    ### HOME > Year > Journal###
} elseif (isset($_GET['journal'])) {
    $journal = (int)$_GET['journal'];

    $sql = 'SELECT Journal FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $journal . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    # navibar

    $name = $year . '_' . $row['Journal'];

    if (mb_strlen($name) > 18) {
        $name = mb_substr($name, 0, 18) . '...';
    }

    $pass = 'year.php~~year=' . $year . '^^journal=' . $journal;

    $navi = "[<a href='year.php?make_s=y&pass=" . $pass . '&name=' . $name . "'>Shortcut</a>]";

    echo '<table style="width:100%">';

    echo '<tr><td style="text-align:right">' . $navi . '</td></tr>';

    echo '<tr><td><a href="index.php">HOME</a> > <a href="year.php?year=' . $year . '">' . $year . '</a> > ' . $row['Journal'] . '</td></tr>';

    echo '</table><hr>';

    # get year's, journal's items

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id');

    $sql .= " WHERE Year='" . $year . "' AND Journal='" . $journal . "'";

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
