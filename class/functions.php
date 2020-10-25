<?php

class functions
{
    public $db;

    public $myts;

    public $user;

    public function __construct($user = 'guest')
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->myts = MyTextSanitizer::getInstance();

        $this->user = $user;
    }

    public function makeShortcut($pass, $name)
    {
        $name = $this->myts->stripSlashesGPC($name);

        $pass = $this->myts->stripSlashesGPC($pass);

        $pass = str_replace('~~', '?', $pass);

        $pass = str_replace('^^', '&', $pass);

        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_shortcut');

        $sql .= " WHERE Usr='" . $this->user . "' AND Target='" . $pass . "'";

        $res = $this->db->query($sql);

        if ($this->db->getRowsNum($res) <= 0) {
            $name = addslashes($name);

            $pass = addslashes($pass);

            $sql = 'INSERT INTO ' . $this->db->prefix('pmid_shortcut');

            $sql .= " VALUES('','" . $this->user . "','" . $name . "','" . $pass . "')";

            $res = $this->db->queryF($sql);
        }

        return $pass;
    }

    #	Substitute special chars (diacriticals) with closest standard ASCII equivalent

    public function special_char_conv($str)
    {
        $char = [];

        if (file_exists('class/special_char.txt')) {
            $file = fopen('class/special_char.txt', 'rb');

            while (!feof($file)) {
                $char[] = fgets($file, 1000);
            }

            fclose($file);

            for ($i = 0, $iMax = count($char); $i < $iMax; $i++) {
                $char2 = explode(',', $char[$i]);

                if (isset($char2[0]) && isset($char2[1])) {
                    $str = str_replace($char2[0], $char2[1], $str);
                }
            }
        }

        return $str;
    }

    # Journal / Book の選択ボックスを表示する

    # $flg=1 でBookフォーム、$flg=0 でJournalフォーム

    # 使用するときはセレクトボックスの名前に注意！

    # selectedJournal0 -> journal

    # selectedJournal1 -> book

    public function getJournal($flg, $journal = '')
    {
        global $xoopsDB;

        $form = "<select name='selectedJournal" . $flg . "'>";

        $form .= "<option label='(unknown)' value='(unknown)'>unknown ";

        (1 == $flg) ? $form .= 'book' : $form .= 'journal';

        $form .= '</option>';

        $label = '(';

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE Book='" . $flg . "' ORDER BY Journal;";

        $res = $xoopsDB->query($sql);

        while (false !== ($row = $xoopsDB->fetchArray($res))) {
            $j = $row['Journal'];

            $l = mb_substr($j, 0, 1);

            if ($l != $label) {
                if ('(' != $label) {
                    $form .= '</optgroup>';
                }

                $label = $l;

                $form .= "<optgroup label='" . $label . "'>";
            }

            if ('(' != $label) {
                $form .= "<option label='" . $j . "' value='" . $j . "'";

                if ($j == $journal) {
                    $form .= ' selected';
                }

                $form .= '>';

                if (mb_strlen($j) > 20) {
                    $j = mb_substr($j, 0, 20) . '..';
                }

                $form .= $j;

                if ($jp = $row['Journal_JP']) {
                    if (mb_strlen($jp) > 20) {
                        $jp = mb_substr($jp, 0, 20) . '..';
                    }

                    $form .= ' / ' . $jp;
                }

                $form .= '</option>';
            }
        }

        $form .= '</select>';

        return $form;
    }
}
