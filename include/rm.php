<?php

# $j = journal name
function rmJournal($j)
{
    global $xoopsDB;

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Journal='" . $j . "'";

    $res = $xoopsDB->query($sql);

    if (!$xoopsDB->getRowsNum($res)) {
        $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $j . "'";

        $res = $xoopsDB->query($sql);

        if ($res) {
            return true;
        }
    }

    return false;
}

# $a = [1], [2], ...
function rmAuthor($a)
{
    global $xoopsDB;

    $ret = '';

    $a = explode(',', mb_substr($a, 0, -1));

    for ($k = 0, $kMax = count($a); $k < $kMax; $k++) {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Author LIKE '%" . $a[$k] . "%'";

        $res = $xoopsDB->query($sql);

        if (!$xoopsDB->getRowsNum($res)) {
            $a[$k] = str_replace('[', '', $a[$k]);

            $a[$k] = str_replace(']', '', $a[$k]);

            $sql = 'SELECT Author FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE id='" . $a[$k] . "'";

            $res = $xoopsDB->query($sql);

            $row = $xoopsDB->fetchArray($res);

            $author = $row['Author'];

            $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE id='" . $a[$k] . "'";

            $res = $xoopsDB->query($sql);

            if ($res) {
                $ret .= $author . ', ';
            }
        }
    }

    if ($ret) {
        return $ret;
    }

    return false;
}
