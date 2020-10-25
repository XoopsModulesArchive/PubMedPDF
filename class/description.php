<?php

class description
{
    public $db;

    public $user;

    public function __construct($user = 'guest')
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->user = $user;
    }

    public function getPaperInfo($id, $mode = 'id')
    {
        $data = [];

        if ('id' == $mode) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_id') . " WHERE id='" . $id . "'";

            $res = $this->db->query($sql);

            if (!$this->db->getRowsNum($res)) {
                return false;
            }

            $row = $this->db->fetchArray($res);

            $data['id'] = $id;

            $data['pmid'] = $row['PMID'];
        } else {
            $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_id') . " WHERE PMID='" . $id . "'";

            $res = $this->db->query($sql);

            if (!$this->db->getRowsNum($res)) {
                return false;
            }

            $row = $this->db->fetchArray($res);

            $data['id'] = $row['id'];

            $data['pmid'] = $id;
        }

        $data['author'] = $this->getAuthorName($row['Author']);

        $data['author_jp'] = $row['Author_JP'];

        $data['author_id'] = $row['Author'];

        $data['journal'] = $this->getJournalName($row['Journal']);

        $data['journal_jp'] = $this->getJournalName($row['Journal'], 'jp');

        $data['journal_id'] = $row['Journal'];

        $data['title'] = $row['Title'];

        $data['title_jp'] = $row['Title_JP'];

        $data['v'] = $row['Volume'];

        $data['pp'] = $row['Page'];

        $data['year'] = $row['Year'];

        $data['f_num'] = $row['F_num'];

        $data['abst'] = $row['Abstract'];

        $data['reg_usr'] = $row['R_usr'];

        $data['reg_date'] = $row['R_date'];

        $data['c_t1'] = $row['Custom_t1'];

        return $data;
    }

    public function getAuthorName($aid)
    {
        $author = '';

        $a = explode(',', mb_substr($aid, 0, -1));

        for ($k = 0, $kMax = count($a); $k < $kMax; $k++) {
            $a[$k] = str_replace('[', '', $a[$k]);

            $a[$k] = str_replace(']', '', $a[$k]);

            $sql = 'SELECT Author FROM ' . $this->db->prefix('pmid_author') . " WHERE id='" . $a[$k] . "'";

            $res = $this->db->query($sql);

            $row = $this->db->fetchArray($res);

            $author .= $row['Author'] . ', ';
        }

        return mb_substr($author, 0, -2);
    }

    public function getJournalName($jid, $type = '')
    {
        $sql = 'SELECT Journal,Journal_JP FROM ' . $this->db->prefix('pmid_journal') . " WHERE id='" . $jid . "'";

        $res = $this->db->query($sql);

        $row = $this->db->fetchArray($res);

        if ('jp' == $type) {
            return $row['Journal_JP'];
        }

        return $row['Journal'];
    }

    # $id = array of paper id

    # $pdfdir = relative path to the PDF dir

    # $check_id = array of id for checkbox

    public function getPaper($id, $pdfdir, $check_id = '')
    {
        $myts = MyTextSanitizer::getInstance();

        $ret = '';

        for ($i = 0, $iMax = count($id); $i < $iMax; $i++) {
            if (!$data = $this->getPaperInfo($id[$i])) {
                continue;
            }

            if (!empty($data['author_jp'])) {
                $a4show = htmlspecialchars($data['author_jp'], ENT_QUOTES | ENT_HTML5);
            } else {
                $a4show = htmlspecialchars($data['author'], ENT_QUOTES | ENT_HTML5);
            }

            if (!empty($data['journal_jp'])) {
                $j4show = htmlspecialchars($data['journal_jp'], ENT_QUOTES | ENT_HTML5);
            } else {
                $j4show = htmlspecialchars($data['journal'], ENT_QUOTES | ENT_HTML5);
            }

            if (!empty($data['title_jp'])) {
                $t4show = htmlspecialchars($data['title_jp'], ENT_QUOTES | ENT_HTML5);
            } else {
                $t4show = htmlspecialchars($data['title'], ENT_QUOTES | ENT_HTML5);
            }

            $info = htmlspecialchars($data['year'] . '-v' . $data['v'] . '-pp' . $data['pp'], ENT_QUOTES | ENT_HTML5);

            # download

            $show_dl = "[<a href='paper_desc.php?id=" . $id[$i] . "'>" . _MD_DESCRIPTION . '</a>] ';

            $class = 'even';

            if ($data['pmid'] > 0) {
                if (file_exists($pdfdir . '/' . $data['pmid'] . '.pdf')) {
                    $show_dl .= "[<a href='" . $pdfdir . '/' . $data['pmid'] . ".pdf' target='_blank'>PDF</a>]";

                    $class = 'head';
                }
            } else {
                require_once __DIR__ . '/include/wopmid.php';

                $file_name = wopmid($data['year'], $data['author'], $data['journal'], $data['v'], $data['pp'], $data['title']) . '.pdf';

                if (file_exists($pdfdir . '/wopmid/' . $file_name)) {
                    $show_dl .= "[<a href='" . $pdfdir . '/wopmid/' . $file_name . "' target='_blank'>PDF</a>]";

                    $class = 'head';
                }
            }

            #note

            $memo = "[<a href='note.php?mid=" . $data['pmid'] . "' target='_blank'>note ";

            $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_memo');

            #private note num

            $res = $this->db->query($sql . " WHERE data_id='" . $data['pmid'] . "' AND Public_flg='0' AND R_usr='" . $this->user . "'");

            $memo .= ' - ' . $this->db->getRowsNum($res);

            #public note num

            $res = $this->db->query($sql . " WHERE data_id='" . $data['pmid'] . "' AND Public_flg='1'");

            $memo .= '/' . $this->db->getRowsNum($res) . '';

            $memo .= '</a>]';

            # favorite

            $favorite = "[<a href='include/reg_favorite.php?id=" . $id[$i] . "'>favorite - " . $data['f_num'] . '</a>]';

            $ret .= "<table class='outer' style='width:100%'><tr>";

            # for favorite

            if (!empty($check_id)) {
                $ret .= "<td rowspan='4' class='head' style='width:20px'>";

                $ret .= "<input type='checkbox' name='data[]' id='data" . $i . "' value='" . $check_id[$i] . "'></td>";

                $favorite = '';
            }

            $ret .= "<td colspan='2' class='" . $class . "'>" . $t4show . '</td></tr>';

            $ret .= "<tr><td colspan='2' class='even'>" . $a4show . '</td></tr>';

            $ret .= "<tr><td colspan='2' class='even' style='text-align:right'>" . $j4show . ' &nbsp;' . $info . '</td></tr>';

            $ret .= '<tr><td>' . $favorite . ' ' . $memo . "</td><td style='text-align:right'>" . $show_dl . '</td></tr>';

            $ret .= '</table><br>';
        }

        return $ret;
    }

    # show paper infomation for PubMed search

    public function getPaper4PS($data, $pdfdir, $type, $color)
    {
        $myts = MyTextSanitizer::getInstance();

        $a4show = htmlspecialchars($data['author'], ENT_QUOTES | ENT_HTML5);

        $j4show = htmlspecialchars($data['journal'], ENT_QUOTES | ENT_HTML5);

        $t4show = htmlspecialchars($data['title'], ENT_QUOTES | ENT_HTML5);

        $info = htmlspecialchars($data['year'] . '-v' . $data['v'] . '-pp' . $data['pp'], ENT_QUOTES | ENT_HTML5);

        $ret = '';

        $show_dl = '';

        $class = 'even';

        $pmidlink = "<a href='http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=search&term=" . $data['pmid'] . "' target='_blank'>" . $data['pmid'] . '</a>';

        if ('reg' == $type) {
            $show_dl = "[<a href='paper_desc.php?id=" . $data['id'] . "'>" . _MD_DESCRIPTION . '</a>] ';

            if (file_exists($pdfdir . '/' . $data['pmid'] . '.pdf')) {
                $show_dl .= "[<a href='" . $pdfdir . '/' . $data['pmid'] . ".pdf' target='_blank'>PDF</a>]";

                $class = 'head';
            }
        }

        $ret .= "<table class='outer' style='width:100%'><tr>";

        $ret .= "<td rowspan='4' class='head' style='width:20px'>";

        if ('nonreg' == $type) {
            $ret .= "<input type='checkbox' name='uninterest[]' value='" . $data['pmid'] . "'>";
        }

        $ret .= '</td>';

        $ret .= "<td colspan='2' class='" . $class . "' style='color:" . $color . "'>" . $t4show . '</td></tr>';

        $ret .= "<tr><td colspan='2' class='even'>" . $a4show . '</td></tr>';

        $ret .= "<tr><td colspan='2' class='even' style='text-align:right'>" . $j4show . ' &nbsp;' . $info . '</td></tr>';

        $ret .= '<tr><td>PMID[' . $pmidlink . "]</td><td style='text-align:right'>" . $show_dl . '</td></tr>';

        $ret .= '</table><br>';

        return $ret;
    }
}
