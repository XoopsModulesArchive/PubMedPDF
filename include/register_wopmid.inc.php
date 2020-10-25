<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require __DIR__ . '/include/wopmid.php';

$error = '';
if (empty($_POST['title_e']) || empty($_POST['author_e'])) {
    $error = 'Fill in the required form.';

    redirect_header($return_url, 2, $error);
}

$title_e = $myts->stripSlashesGPC($_POST['title_e']);
$author_e = $myts->stripSlashesGPC($_POST['author_e']);

if (!preg_match('^[_A-Za-z0-9]+$', $title_e)) {
    $error = 'You can only use alphabet in English title form.';
} elseif (!preg_match('^[_A-Za-z0-9]+$', $author_e)) {
    $error = 'You can only use alphabet in English author form.';
}
if ($error) {
    redirect_header($return_url, 2, $error);
}

$data = [];
$data['id'] = 0;
$data['t'] = $myts->stripSlashesGPC($_POST['title_e']);
$data['t_jp'] = $myts->stripSlashesGPC($_POST['title_o']);
$data['a'] = $myts->stripSlashesGPC($_POST['author_e']);
$data['a_jp'] = $myts->stripSlashesGPC($_POST['author_o']);
$data['v'] = $myts->stripSlashesGPC($_POST['volume']);
$data['p'] = $myts->stripSlashesGPC($_POST['page']);
$data['ab'] = $myts->stripSlashesGPC($_POST['abstract']);
$data['jb'] = $myts->stripSlashesGPC($_POST['jb']);
$j0 = $myts->stripSlashesGPC($_POST['selectedJournal0']);
$j1 = $myts->stripSlashesGPC($_POST['selectedJournal1']);

# journal/book name
if ('j' == $data['jb']) {
    ('(unknown)' != $j0) ? $data['j'] = $j0 : $data['j'] = '(unknown)';
} elseif ('b' == $data['jb']) {
    ('(unknown)' != $j1) ? $data['j'] = $j1 : $data['j'] = '(unknown)';
}

# year
if (!empty($_POST['year'])) {
    $data['y'] = (int)$_POST['year'];
} elseif (!empty($_POST['selectedYear'])) {
    $data['y'] = (int)$_POST['selectedYear'];
} else {
    $data['y'] = '0000';
}

# author
$Author = '';
$Authors = explode(',', $data['a']);
foreach ($Authors as $a) {
    $Author .= trim($a) . ', ';
}
$data['a'] = mb_substr($Author, 0, -2);

# พรคนคส
foreach ($data as $key => $value) {
    $data[$key] = str_replace('"', '&#34;', $data[$key]);
}

$wopmid_id = wopmid($data['y'], $data['a'], $data['j'], $data['v'], $data['p'], $data['t']);
$data['c_t1'] = $wopmid_id;
