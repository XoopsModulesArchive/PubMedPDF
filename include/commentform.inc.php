<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/include/xoopscodes.php';
$pub = _MD_NOTE_PUB;
$pri = _MD_NOTE_PRI;
$message = _MD_NOTE_MES;
if ($nid) {
    $nattach = _MD_NOTE_ATTACHED_EDIT;
} else {
    $nattach = _MD_NOTE_ATTACHED;
}

echo "<form enctype='multipart/form-data' action='note.php' method='POST'>";
echo <<<E
		<table class='outer'>
		<tr>
			<td class='head'><b>$message</b></td>
			<td class='even'>
E;
xoopsCodeTarea('note');
xoopsSmilies('note');
echo <<<E
			</td>
		</tr>
		<tr>
			<td class='head'><b>$nattach</b></td>
			<td class='even'><input type='file' name='userfile'>
E;
if ($nid) {
    echo "&nbsp;&nbsp;&nbsp;<input type='checkbox' name='del_file'>" . _MD_NOTE_ATTACHED_DEL;
}
echo <<<E
		</td>
		</tr>
		<tr>
			<td class='head'><b>$pub/$pri</b></td>
			<td class='even'>
E;
if (isset($pflg) && 1 == $pflg) {
    echo "<input type='radio' value='1' name='pflg' checked>" . $pub;

    echo "<input type='radio' value='0' name='pflg'>" . $pri;
} else {
    echo "<input type='radio' value='1' name='pflg'>" . $pub;

    echo "<input type='radio' value='0' name='pflg' checked>" . $pri;
}
echo <<<E
			</td>
		</tr>
		<tr>
			<td class='head'> </td>
			<td class='even'>
				<input type='submit' class='button' name='submit' value='submit'>
			</td>
		</tr>
		</table>
		<input type='hidden' name='mode' value='$mode'>
		<input type='hidden' name='mid' value='$mid'>
E;
if ($nid) {
    echo "<input type='hidden' name='nid' value='" . $nid . "'>";
}
echo '</form>';
