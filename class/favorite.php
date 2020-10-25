<?php

class favorite
{
    public $db;

    public $myts;

    public $user;

    public $option;

    public function __construct($user = 'guest')
    {
        $this->db = XoopsDatabaseFactory::getDatabaseConnection();

        $this->myts = MyTextSanitizer::getInstance();

        $this->user = $user;
    }

    # 移動先選択用<option>作成

    public function getDirList($data_id = 0)
    {
        $item_num = '';

        if ($data_id) {
            if ($n = $this->__getItemNum($data_id, 0)) {
                $item_num = '(' . $n . ')';
            }
        }

        $this->option = "<option value='0'>Top directory " . $item_num . '</option>';

        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir');

        $sql .= " WHERE Usr='" . $this->user . "' AND Pass=''";

        $res = $this->db->query($sql);

        while (false !== ($row = $this->db->fetchArray($res))) {
            $item_num = '';

            if ($data_id) {
                if ($n = $this->__getItemNum($data_id, $row['id'])) {
                    $item_num = '(' . $n . ')';
                }
            }
            (1 == $row['Public_flg']) ? $p = '(P)' : $p = '';

            $this->option .= "<option value='" . $row['id'] . "'>" . $row['Name'] . ' ' . $p . ' ' . $item_num . '</option>';

            $this->__getDirList($row['Pass'] . '[' . $row['id'] . ']', 1, $data_id);
        }

        return $this->option;
    }

    public function __getDirList($path, $n, $data_id)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir');

        $sql .= " WHERE Pass='" . $path . "' AND Usr='" . $this->user . "'";

        $rs = $this->db->query($sql);

        while (false !== ($row = $this->db->fetchArray($rs))) {
            $item_num = '';

            if ($data_id) {
                if ($n = $this->__getItemNum($data_id, $row['id'])) {
                    $item_num = '(' . $n . ')';
                }
            }

            $this->option .= "<option value='" . $row['id'] . "'>";

            for ($i = 0; $i < $n; $i++) {
                $this->option .= '--';
            }
            (1 == $row['Public_flg']) ? $p = '(P)' : $p = '';

            $this->option .= $row['Name'] . ' ' . $p . ' ' . $item_num . '</option>';

            $n++;

            $this->option .= $this->__getDirList($row['Pass'] . '[' . $row['id'] . ']', $n, $data_id);

            $n--;
        }
    }

    public function __getItemNum($data_id, $dir_id)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_data');

        $sql .= " WHERE data_id='" . $data_id . "' AND dir_id='" . $dir_id . "' AND Usr='" . $this->user . "'";

        $res = $this->db->query($sql);

        return $this->db->getRowsNum($res);
    }

    # ディレクトリ一覧作成

    public function getDir($dir_id = 0)
    {
        $ret = '';

        $path = '';

        if (0 != $dir_id) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir') . " WHERE id='" . $dir_id . "'";

            $res = $this->db->query($sql);

            $row = $this->db->fetchArray($res);

            $path = $row['Pass'] . '[' . $dir_id . ']';
        }

        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir');

        $sql .= " WHERE Usr='" . $this->user . "' AND Pass='" . $path . "'";

        $res = $this->db->query($sql);

        while (false !== ($row = $this->db->fetchArray($res))) {
            $dir_id = $row['id'];

            $dirname = $this->myts->htmlSpecialChars($row['Name']);

            $datanum = $this->getDataNum($dir_id);

            (1 == $row['Public_flg']) ? $p = ' (P)' : $p = '';

            $ret .= "[<a href='favorite.php?del_id=" . $dir_id . "'><img src='images/delete.png'></a>] ";

            $ret .= "<a href='favorite.php?dir_id=" . $dir_id . "'>" . $dirname . '</a> (' . $datanum . ')' . $p;

            $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir');

            $sql .= " WHERE Usr='" . $this->user . "' AND Pass like '%[" . $dir_id . "]%'";

            $res2 = $this->db->query($sql);

            if ($this->db->getRowsNum($res2)) {
                $ret .= "<table style='margin-left:20px'><tr>";

                $i = 0;

                while (false !== ($row2 = $this->db->fetchArray($res2))) {
                    $i++;

                    $dir_id = $row2['id'];

                    $dirname = $this->myts->htmlSpecialChars($row2['Name']);

                    $datanum = $this->getDataNum($dir_id);

                    (1 == $row2['Public_flg']) ? $p = ' (P)' : $p = '';

                    $ret .= "<td style='width:20%'>";

                    $ret .= "[<a href='favorite.php?del_id=" . $dir_id . "'><img src='images/delete.png'></a>] ";

                    $ret .= "<a href='favorite.php?dir_id=" . $dir_id . "'>" . $dirname . '</a> (' . $datanum . ')' . $p;

                    $ret .= '</td>';

                    if (!($i % 4)) {
                        $ret .= '</tr><tr>';
                    }
                }

                $ret .= '</tr></table><br>';
            } else {
                $ret .= '<br>';
            }
        }

        return $ret;
    }

    # ディレクトリ一覧（公開）作成

    public function getPublicDir()
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir');

        $sql .= " WHERE Public_flg='1' ORDER BY Usr";

        $res = $this->db->query($sql);

        $ret = "<table style='margin-left:20px'><tr>";

        $i = 0;

        while (false !== ($row = $this->db->fetchArray($res))) {
            $i++;

            $ret .= "<td style='width:33%'>[" . $row['Usr'] . '] ';

            $ret .= "<a href='favorite_pub.php?dir_id=" . $row['id'] . "'>" . $row['Name'] . '</a> ';

            $ret .= '(' . $this->getDataNum($row['id']) . ')';

            $ret .= '</td>';

            if (!($i % 3)) {
                $ret .= '</tr><tr>';
            }
        }

        $ret .= '</tr></table><br>';

        return $ret;
    }

    # ディレクトリに含まれるデータ個数取得

    public function getDataNum($dir_id = 0)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_data');

        $sql .= " WHERE dir_id='" . $dir_id . "'";

        $res = $this->db->query($sql);

        return $this->db->getRowsNum($res);
    }

    # ディレクトリのパンくずリストを作成

    public function getDirNavi($dir_id = 0)
    {
        $ret = "<a href='favorite.php?dir_id=0'>" . _MD_FAVORITE_DIRTOP . '</a>';

        if (0 == $dir_id) {
            return $ret;
        }

        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir') . " WHERE id='" . $dir_id . "'";

        $res = $this->db->query($sql);

        $row = $this->db->fetchArray($res);

        $path = $row['Pass'] . '[' . $dir_id . ']';

        $token = explode('][', $path);

        for ($i = 0, $iMax = count($token); $i < $iMax; $i++) {
            $token[$i] = str_replace('[', '', $token[$i]);

            $token[$i] = str_replace(']', '', $token[$i]);

            $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir') . " WHERE id='" . $token[$i] . "'";

            $res = $this->db->query($sql);

            $row = $this->db->fetchArray($res);

            $dirname = $this->myts->htmlSpecialChars($row['Name']);

            $ret .= "/<a href='favorite.php?dir_id=" . $row['id'] . "'>" . $dirname . '</a>';
        }

        return $ret;
    }

    # ディレクトリに含まれるデータIDを取得

    public function getData($dir_id = 0, $pub = '')
    {
        $dir_id = (int)$dir_id;

        $data_id = [];

        $fdata_id = [];

        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_data');

        $sql .= " WHERE dir_id='" . $dir_id . "'";

        if ('pub' != $pub) {
            $sql .= " AND Usr='" . $this->user . "'";
        }

        $res = $this->db->query($sql);

        while (false !== ($row = $this->db->fetchArray($res))) {
            $data_id[] = $row['data_id'];

            $fdata_id[] = $row['id'];
        }

        return [$data_id, $fdata_id];
    }

    # ディレクトリ（公開）に含まれる全てのデータIDを取得

    public function getPublicData()
    {
        $data_id = [];

        $fdata_id = [];

        $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_dir');

        $sql .= " WHERE Public_flg='1' ORDER BY Usr";

        $res = $this->db->query($sql);

        while (false !== ($row = $this->db->fetchArray($res))) {
            $sql = 'SELECT * FROM ' . $this->db->prefix('pmid_favorite_data');

            $sql .= " WHERE dir_id='" . $row['id'] . "'";

            $res2 = $this->db->query($sql);

            while (false !== ($row2 = $this->db->fetchArray($res2))) {
                $data_id[] = $row2['data_id'];

                $fdata_id[] = $row2['id'];
            }
        }

        return [$data_id, $fdata_id];
    }
}
