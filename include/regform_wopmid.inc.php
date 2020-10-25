<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require __DIR__ . '/class/functions.php';
$fn = new functions();

if ('edit' == $mode) {
    foreach ($data as $k => $v) {
        $data[$k] = htmlspecialchars($v, ENT_QUOTES | ENT_HTML5);
    }

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $data['journal_id'] . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $book = $row['Book'];
} else {
    $data['title'] = '';

    $data['title_jp'] = '';

    $data['author'] = '';

    $data['author_jp'] = '';

    $data['journal'] = '';

    $data['v'] = '';

    $data['pp'] = '';

    $data['year'] = '';

    $data['abst'] = '';

    $book = 0;
}

# Title
echo "<tr><td colspan='2' style='text-align:left'><b>Title</b></td></tr>";
echo "<tr><td style='width:140px' >&nbsp;&nbsp;*in English</td>";
echo "<td><input type='text' style='width:90%' name='title_e' value='" . $data['title'] . "'></td></tr>";
echo '<tr><td>&nbsp;&nbsp; in other Language</td>';
echo "<td><input type='text' style='width:90%' name='title_o' value='" . $data['title_jp'] . "'></td></tr>";

# author
echo "<tr><td colspan='2' style='text-align:left'><b>";
echo "<span title='eg Ikeno H, Nishioka T, ...'>Authors</span></b></td></tr>";
echo '<tr><td>&nbsp;&nbsp;*in English</td>';
echo "<td><input type='text' style='width:90%' name='author_e' value='" . $data['author'] . "'></td></tr>";
echo '<tr><td>&nbsp;&nbsp; in other Language</td>';
echo "<td><input type='text' style='width:90%' name='author_o' value='" . $data['author_jp'] . "'></td>";
echo '</tr>';

# journal
echo "<tr><td colspan='2' style='text-align:left\;'><b>Journal/Book</b></td></tr>";
echo '<tr><td> </td>';

echo "<td><input type='radio' name='jb' value='j'";
if (0 == $book) {
    echo 'checked';
}
echo '>';
echo $fn->getJournal(0, $data['journal']);

echo "<br><input type='radio' name='jb' value='b'";
if (1 == $book) {
    echo 'checked';
}
echo '>';
echo $fn->getJournal(1, $data['journal']);
echo '<br><br></td></tr>';

# volume
echo '<tr><td><b>Volume</b></td>';
echo "<td><input type='text' size='24' name='volume' value='" . $data['v'] . "'></td>";
echo '</tr>';

# page
echo '<tr><td><b>Pages</b></td>';
echo "<td><input type='text' size='24' name='page' value='" . $data['pp'] . "'></td>";
echo '</tr>';

# year
if ($data['year'] > 0) {
    echo '<tr><td><b>Year</b></td>';

    if ('0000' != $data['year']) {
        echo "<td><input type='text' size='24' name='year' value='" . $data['year'] . "'>";
    } else {
        echo "<td><input type='text' size='24' name='year'>";
    }

    echo '</td>';

    echo '</tr>';
} else {
    echo "<tr><td colspan='2' style='text-align:left'><b>Year</b></td></tr>";

    echo '<tr><td>&nbsp;&nbsp;select year</td><td>';

    echo "<select name='selectedYear'>";

    echo "<option value='0' selected>(unkown)";

    for ($i = 2006; $i > 1980; $i--) {
        echo "<option value='" . $i . "'>" . $i;
    }

    echo '</select></td></tr>';

    echo '<tr><td>&nbsp;&nbsp;other year</td><td>';

    echo "<input type='text' size='14' name='year'>";

    echo '</td></tr>';
}

# abst
echo '<tr><td><b>Abstract</b></td>';
echo "<td><textarea name='abstract' rows='10' style='width:90%' >" . $data['abst'] . '</textarea></td>';
echo '</tr>';
