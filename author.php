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

### HOME > Initial ###
if (isset($_GET['initial'])) {
    $initial = ($_GET['initial']);

    # navibar

    $name = 'Author [' . $initial . ']';

    $pass = 'author.php~~initial=' . $initial;

    $navi = "[<a href='author.php?make_s=y&pass=" . $pass . '&name=' . $name . "'>Shortcut</a>]";

    echo '<table style="width:100%">';

    echo '<tr><td style="text-align:right">' . $navi . '</td></tr>';

    echo '<tr><td><a href="index.php">HOME</a> > ' . $initial . '</td></tr>';

    echo '</table><hr>';

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE Author like '" . $initial . "%' ORDER BY Author";

    $res = $xoopsDB->query($sql);

    $i = 0;

    $td = 0;

    echo '<table><tr>';

    while (false !== ($row = $xoopsDB->fetchArray($res))) {
        # get author's items

        $tmp2 = [];

        $sql2 = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Author like '%[" . $row['id'] . "]%' ORDER BY Year DESC";

        $res2 = $xoopsDB->query($sql2);

        while (false !== ($row2 = $xoopsDB->fetchArray($res2))) {
            $tmp = $row2['Author'] . $row2['Title'];

            if (!in_array($tmp, $tmp2, true)) {
                $tmp2[] = $tmp;

                $item[$i] = $row2['id'];

                $i++;
            }
        }

        if (!($td % 4)) {
            echo '</tr><tr>';
        }

        echo '<td>';

        echo '<a href="author.php?author=' . $row['id'] . '">' . $row['Author'] . '</a> (' . $GLOBALS['xoopsDB']->getRowsNum($res2) . ') &nbsp;';

        echo '</td>';

        $td++;
    }

    echo '</tr></table>';

    echo '<hr>';

#echo $desc->getPaper($item, PDFDIR2);

    ### HOME > Initial > Author ###
} elseif (isset($_GET['author']) && !isset($_GET['year'])) {
    $id = (int)$_GET['author'];

    $sql = 'select * from ' . $xoopsDB->prefix('pmid_author') . " where id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $author = $row['Author'];

    $initial = mb_substr($author, 0, 1);

    # navibar

    $name = $author;

    $pass = 'author.php~~author=' . $id;

    $navi = "[<a href='author.php?make_s=y&pass=" . $pass . '&name=' . $name . "'>Shortcut</a>]";

    echo '<table style="width:100%">';

    echo '<tr><td style="text-align:right">' . $navi . '</td></tr>';

    echo '<tr><td><a href="index.php">HOME</a> > <a href="author.php?initial=' . $initial . '">' . $initial . '</a> > ' . $author . '</td></tr>';

    echo '</table><hr>';

    # get author's items

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Author like '%[" . $id . "]%' ORDER BY Year DESC";

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
        echo '<a href="author.php?author=' . $id . '&year=' . $key . '">' . $key . '</a> (' . $value . ') &nbsp;';
    }

    echo '<hr>';

    echo $desc->getPaper($item, PDFDIR2);

### HOME > Initial > Author > Year ###
} elseif (isset($_GET['year'])) {
    $year = (int)$_GET['year'];

    $id = (int)$_GET['author'];

    $sql = 'select * from ' . $xoopsDB->prefix('pmid_author') . " where id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $author = $row['Author'];

    $initial = mb_substr($author, 0, 1);

    # navibar

    $name = $author . '_' . $year;

    $pass = 'author.php~~author=' . $id . '^^year=' . $year;

    $navi = "[<a href='author.php?make_s=y&pass=" . $pass . '&name=' . $name . "'>Shortcut</a>]";

    echo '<table style="width:100%">';

    echo '<tr><td style="text-align:right">' . $navi . '</td></tr>';

    echo '<tr><td><a href="index.php">HOME</a> > <a href="author.php?initial=' . $initial . '">' . $initial . '</a> > <a href="author.php?author=' . $id . '">' . $author . '</a> > ' . $year . '</td></tr>';

    echo '</table><hr>';

    # get author's items

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Author like '%[" . $id . "]%' AND Year='" . $year . "' ORDER BY PMID DESC";

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
