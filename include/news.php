<?php

#-----------------------------------------------
# NEWS_REGISTER( category, title, body, user)
# register news into news modules table
#-----------------------------------------------

function NEWS_REGISTER($news_topic, $title, $body, $user)
{
    global $xoopsDB;

    $title = addslashes($title);

    $body = addslashes($body);

    # whether news module exists or not

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('modules') . " WHERE dirname='news'";

    $res = $xoopsDB->query($sql);

    if ($xoopsDB->getRowsNum($res) > 0) {
        $time = time();

        # topic PubMedPDF

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('topics') . " WHERE topic_title='PubMedPDF'";

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res) <= 0) {
            $sql = 'INSERT INTO ' . $xoopsDB->prefix('topics') . " VALUES('','0','','PubMedPDF')";

            $res = $xoopsDB->query($sql);
        }

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('topics') . " WHERE topic_title='PubMedPDF'";

        $res = $xoopsDB->query($sql);

        $row = $xoopsDB->fetchArray($res);

        $pid = $row['topic_id'];

        # subtopic

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('topics') . " WHERE topic_title='" . $news_topic . "'";

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res) <= 0) {
            $sql = 'INSERT INTO ' . $xoopsDB->prefix('topics') . " VALUES('','" . $pid . "','','" . $news_topic . "')";

            $res = $xoopsDB->query($sql);
        }

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('topics') . " WHERE topic_title='" . $news_topic . "'";

        $res = $xoopsDB->query($sql);

        $row = $xoopsDB->fetchArray($res);

        $pid = $row['topic_id'];

        # reg news

        $sql = 'INSERT INTO ' . $xoopsDB->prefix('stories') . " VALUES('','1','" . $title . "','" . $time . "','" . $time . "','0','','1','1','" . $body . "','','0','" . $pid . "','0','0','" . $user . "','0','R','0')";

        $res = $xoopsDB->query($sql);
    }
}
