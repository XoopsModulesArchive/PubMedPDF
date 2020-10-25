<?php

#-----------------------------------------------------------
# register/edit paper
#
# $array[
# id, j(journal), y(year), v(vol), p(page), ab(abstract), t(title), a(author),
# t_jp(title_jp), j_jp(journal_jp), a_jp(author_jp),
# ]
#
# $xoops user name
#
# return: id number
#------------------------------------------------------------

function DB($data, $user)
{
    global $xoopsDB;

    $day = getdate();

    $date = $day['year'] . '-' . $day['mon'] . '-' . $day['mday'];

    foreach ($data as $key => $value) {
        $data[$key] = addslashes($data[$key]);
    }

    # Journal

    $sql = 'SELECT id FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE Journal='" . $data['j'] . "'";

    $res = $xoopsDB->query($sql);

    if (!$xoopsDB->getRowsNum($res)) {
        $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_journal');

        $sql .= " VALUES('','" . $data['j'] . "','','','0','','','','')";

        $res = $xoopsDB->query($sql);

        $jid = $xoopsDB->getInsertId($res);
    } else {
        $row = $xoopsDB->fetchArray($res);

        $jid = $row['id'];
    }

    # Author

    $Author = explode(', ', $data['a']);

    $aid = '';

    for ($i = 0, $iMax = count($Author); $i < $iMax; $i++) {
        $sql = 'SELECT id FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE Author='" . $Author[$i] . "'";

        $res = $xoopsDB->query($sql);

        if (!$xoopsDB->getRowsNum($res)) {
            $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_author') . " VALUES('','" . $Author[$i] . "','','')";

            $res = $xoopsDB->query($sql);

            $aid .= '[' . $xoopsDB->getInsertId($res) . '],';
        } else {
            $row = $xoopsDB->fetchArray($res);

            $aid .= '[' . $row['id'] . '],';
        }
    }

    # Year

    if ('0' == $data['y']) {
        $data['y'] = '0000';
    }

    # with PMID

    if (!isset($data['c_t1'])) {
        $data['t_jp'] = '';

        $data['a_jp'] = '';

        $data['c_t1'] = '';
    }

    # Pmid

    $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_id') . ' VALUES(';

    $sql .= "'','" . $data['id'] . "','" . $jid . "','" . $data['y'] . "','" . $data['v'] . "','" . $data['p'] . "',";

    $sql .= "'" . $aid . "','" . $data['t'] . "','" . $data['ab'] . "','" . $user . "','" . $date . "',";

    $sql .= "'','','" . $data['t_jp'] . "','" . $data['a_jp'] . "','" . $data['c_t1'] . "','','','','','')";

    $res = $xoopsDB->query($sql);

    return $xoopsDB->getInsertId($res);
}

function MOD_DB($id, $data)
{
    global $xoopsDB;

    foreach ($data as $key => $value) {
        $data[$key] = addslashes($value);
    }

    # Journal

    $sql = 'SELECT id FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE Journal='" . $data['j'] . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $jid = $row['id'];

    # Author

    $Author = explode(', ', $data['a']);

    $aid = '';

    for ($i = 0, $iMax = count($Author); $i < $iMax; $i++) {
        $sql = 'SELECT id FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE Author='" . $Author[$i] . "'";

        $res = $xoopsDB->query($sql);

        if (!$xoopsDB->getRowsNum($res)) {
            $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_author') . " VALUES('','" . $Author[$i] . "','','')";

            $res = $xoopsDB->query($sql);

            $aid .= '[' . $xoopsDB->getInsertId($res) . '],';
        } else {
            $row = $xoopsDB->fetchArray($res);

            $aid .= '[' . $row['id'] . '],';
        }
    }

    # Year

    if ('0' == $data['y']) {
        $data['y'] = '0000';
    }

    # Pmid

    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_id') . ' SET ';

    $sql .= "Journal='" . $jid . "', Year='" . $data['y'] . "', Volume='" . $data['v'] . "',";

    $sql .= "Page='" . $data['p'] . "', Author='" . $aid . "', Title='" . $data['t'] . "',";

    $sql .= "Abstract='" . $data['ab'] . "', Title_JP='" . $data['t_jp'] . "', Author_JP='" . $data['a_jp'] . "', Custom_t1='" . $data['c_t1'] . "' ";

    $sql .= "WHERE id='" . $id . "'";

    $res = $xoopsDB->query($sql);

    if ($res) {
        return true;
    }

    return false;
}

#-----------------------------------------------------------
# register Journal
#
# array[
# j(journal), j_jp(journal_jp), j_url(journal_url),
# bk(book flg), act(active flg), e(editor), pub(publisher)
# ]
#
# return: id number
#------------------------------------------------------------

function REG_JOURNAL($data)
{
    global $xoopsDB;

    foreach ($data as $key => $value) {
        $data[$key] = addslashes($data[$key]);
    }

    $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_journal') . ' VALUES(';

    $sql .= "'','" . $data['j'] . "','" . $data['j_jp'] . "','" . $data['j_url'] . "','" . $data['bk'] . "','" . $data['e'] . "','" . $data['pub'] . "','','')";

    $res = $xoopsDB->query($sql);

    return $xoopsDB->getInsertId($res);
}

function EDIT_JOURNAL($data)
{
    global $xoopsDB;

    foreach ($data as $key => $value) {
        $data[$key] = addslashes($data[$key]);
    }

    $sql = 'UPDATE ' . $xoopsDB->prefix('pmid_journal') . " SET Journal='" . $data['j'] . "',Journal_JP='" . $data['j_jp'] . "',";

    $sql .= "URL='" . $data['j_url'] . "',Book='" . $data['bk'] . "',Editor='" . $data['e'] . "', Publisher='" . $data['pub'] . "'";

    $sql .= " WHERE id='" . $data['id'] . "'";

    $res = $xoopsDB->query($sql);

    return $xoopsDB->getInsertId($res);
}
