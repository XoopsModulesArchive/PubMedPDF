<?php

require __DIR__ . '/nheader.php';
require __DIR__ . '/class/functions.php';

require XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/style.php';

$t = 'checked';
$a = 'checked';
$y = '';
$j = '';
$auth = '';
$journal = '';
$u = '';
$yfrom = '';
$yto = '';
$key = '';
$aname = '';
$and = 'checked';
$or = '';
$uid = 0;

if (isset($_GET['Go']) || isset($_GET['p'])) {
    if (!empty($_GET['t'])) {
        $t = 'checked';
    }

    if (!empty($_GET['a'])) {
        $a = 'checked';
    }

    if (!empty($_GET['y'])) {
        $y = 'checked';
    }

    if (!empty($_GET['j'])) {
        $j = 'checked';
    }

    if (!empty($_GET['u'])) {
        $u = 'checked';
    }

    if (!empty($_GET['auth'])) {
        $auth = 'checked';
    }

    $aname = $myts->stripSlashesGPC($_GET['aname']);

    $key = $myts->stripSlashesGPC($_GET['key']);

    if (!empty($_GET['yfrom'])) {
        $yfrom = (int)$_GET['yfrom'];
    }

    if (!empty($_GET['yto'])) {
        $yto = (int)$_GET['yto'];
    }

    if (isset($_GET['Go'])) {
        if (!empty($_GET['selectedJournal0'])) {
            $journal = $_GET['selectedJournal0'];
        }
    } else {
        $journal = $myts->stripSlashesGPC($_GET['journal']);
    }

    $mt = $_GET['mt'];

    if ('and' == $mt) {
        $and = 'checked';

        $or = '';
    } else {
        $and = '';

        $or = 'checked';
    }

    $uid = (int)$_GET['uid'];
}

## 検索フォーム
echo "<div class='pt'>" . _MI_LOCALSEARCH . '</div>';
echo "<form action='' method='GET'>";
echo "<div class='pc'><table style='width:95%'>";

echo "<tr><td style='width:70px'><b>Word</b></td>";
echo "<td><input type='text' style='width:95%' name='key' value='" . htmlspecialchars($key, ENT_QUOTES | ENT_HTML5) . "'></td></tr>";

echo '<tr><td><b>Target</b></td><td><table><tr>';
echo "<td style='width:90px'><input type='checkbox' name='t' value='y' " . $t . '> Title</td>';
echo "<td><input type='checkbox' name='a' value='y' " . $a . '> Abstract</td></tr></table></td></tr>';

echo '<tr><td><b>Option</b></td><td><table>';
echo "<tr><td style='width:100px'><input type='checkbox' name='auth' value='y' " . $auth . '> Author</td>';
echo "<td><input type='text' name='aname' style='width:200px' value='" . htmlspecialchars($aname, ENT_QUOTES | ENT_HTML5) . "'></td></tr>";

echo "<tr><td><input type='checkbox' name='j' value='y' " . $j . '> Journal</td><td>';
$fn = new functions();
echo $fn->getJournal(0, $journal);
echo '</td></tr>';

echo "<tr><td><input type='checkbox' name='y' value='y' " . $y . '> Year</td>';
echo "<td>From <input type='text' size='5' name='yfrom' value='" . $yfrom . "'> ";
echo "to <input type='text' size='5' name='yto' value='" . $yto . "'></td></tr>";

echo "<tr><td><input type='checkbox' name='u' value='y' " . $u . '> Reg User</td>';
echo "<td><select name='uid'>";
$sql = 'SELECT uid, uname FROM ' . $xoopsDB->prefix('users') . ' ORDER BY uid';
$res = $xoopsDB->query($sql);
while (false !== ($row = $xoopsDB->fetchArray($res))) {
    echo "<option value='" . $row['uid'] . "'";

    if ($row['uid'] == $uid) {
        echo ' selected';
    }

    echo '>' . $row['uname'] . '</option>';
}
echo '</select></td></tr>';
echo '</table></td></tr>';

echo '<tr><td><b>Method</b></td><td>';
echo "<input type='radio' name='mt' value='and' " . $and . '> AND ';
echo "<input type='radio' name='mt' value='or' " . $or . '> OR</tr>';

echo '<tr><td><br><b>Submit</b></td>';
echo "<td><br><input type='submit' value='submit' name='Go'></td></tr></table>";
echo '</div></form>';

if (isset($_GET['p'])) {
    $if = [$t, $a, $y, $yfrom, $yto, $j, $journal, $key, $mt, $auth, $aname, $u, $uid];

    showResult($user, (int)$_GET['time'], $if, (int)$_GET['p']);
}

## 検索処理
if (isset($_GET['Go'])) {
    $result = [];

    $result_t = []; #title
    $result_a = []; #abst

    $result_aoption = []; #options
    $result_joption = []; #options
    $result_yoption = []; #options

    $keyword = explode(' ', $key);

    $key_num = count($keyword);

    # author

    $author = 0;

    $a_id = [];

    $a_where = '';

    if (mb_strstr($aname, ',')) {
        $a_key = explode(',', $aname);

        for ($i = 0, $iMax = count($a_key); $i < $iMax; $i++) {
            $a_key[$i] = trim($a_key[$i]);
        }
    } else {
        $a_key = explode(' ', $aname);
    }

    if (!empty($auth)) {
        $author = 1;

        for ($i = 0, $iMax = count($a_key); $i < $iMax; $i++) {
            if ('' == $a_key[$i]) {
                continue;
            }

            $sql = 'SELECT id FROM ' . $xoopsDB->prefix('pmid_author') . " WHERE Author like '" . $a_key[$i] . "%'";

            $res = $xoopsDB->query($sql);

            if ($xoopsDB->getRowsNum($res)) {
                while (false !== ($row = $xoopsDB->fetchArray($res))) {
                    if (!in_array($row['id'], $a_id, true)) {
                        $a_id[] = $row['id'];
                    }
                }
            }
        }

        for ($i = 0, $iMax = count($a_id); $i < $iMax; $i++) {
            $a_where .= " author like '%[" . $a_id[$i] . "]%' ";

            $a_where .= ' or ';
        }

        if ('' != $a_where) {
            $a_where = mb_substr($a_where, 0, -3);

            $a_where = '(' . $a_where . ')';
        }

        $sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . ' where ' . $a_where;

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res)) {
            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                if (!in_array($row['id'], $result_aoption, true)) {
                    $result_aoption[] = $row['id'];
                }
            }
        }
    }

    # year

    $year = 0;

    if (!empty($y)) {
        $year = 1;

        if (empty($yfrom)) {
            $yfrom = 0000;
        }

        if (empty($yto)) {
            $yto = 9999;
        }

        $y_where = "Year >= '" . $yfrom . "' and Year <= '" . $yto . "'";

        $sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . ' where ' . $y_where;

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res)) {
            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                if (!in_array($row['id'], $result_yoption, true)) {
                    $result_yoption[] = $row['id'];
                }
            }
        }
    }

    # journal

    $jflg = 0;

    if (!empty($j)) {
        $jflg = 1;

        $sql = 'select * from ' . $xoopsDB->prefix('pmid_journal') . " where Journal='" . $journal . "'";

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res)) {
            $row = $xoopsDB->fetchArray($res);

            $j_id = $row['id'];

            $j_where = "Journal='" . $j_id . "'";
        }

        $sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . ' where ' . $j_where;

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res)) {
            while (false !== ($row = $xoopsDB->fetchArray($res))) {
                if (!in_array($row['id'], $result_joption, true)) {
                    $result_joption[] = $row['id'];
                }
            }
        }
    }

    #print_r($result_aoption);

    #print_r($result_joption);

    #print_r($result_yoption);

    $u_where = '';

    if (!empty($u)) {
        $sql = 'SELECT uname FROM ' . $xoopsDB->prefix('users') . " WHERE uid='" . $uid . "'";

        $res = $xoopsDB->query($sql);

        $row = $xoopsDB->fetchArray($res);

        $u_where = " AND R_usr='" . $row['uname'] . "'";
    }

    # 各キーワードごとに検索をかけ、AND用に引っかかった回数を記録

    for ($i = 0; $i < $key_num; $i++) {
        if (!empty($t)) {
            ## title

            $sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . " where Title like '%" . $keyword[$i] . "%'" . $u_where;

            $res = $xoopsDB->query($sql);

            if ($xoopsDB->getRowsNum($res)) {
                while (false !== ($row = $xoopsDB->fetchArray($res))) {
                    $pmid = $row['id'];

                    if ($author) {
                        if ($jflg && $year) {
                            if (in_array($pmid, $result_aoption, true) && in_array($pmid, $result_joption, true) && in_array($pmid, $result_yoption, true)) {
                                if (!isset($result_t[$pmid])) {
                                    $result_t[$pmid] = 0;
                                }

                                $result_t[$pmid]++;
                            }
                        } elseif ($jflg) {
                            if (in_array($pmid, $result_aoption, true) && in_array($pmid, $result_joption, true)) {
                                if (!isset($result_t[$pmid])) {
                                    $result_t[$pmid] = 0;
                                }

                                $result_t[$pmid]++;
                            }
                        } elseif ($year) {
                            if (in_array($pmid, $result_aoption, true) && in_array($pmid, $result_yoption, true)) {
                                if (!isset($result_t[$pmid])) {
                                    $result_t[$pmid] = 0;
                                }

                                $result_t[$pmid]++;
                            }
                        } else {
                            if (in_array($pmid, $result_aoption, true)) {
                                if (!isset($result_t[$pmid])) {
                                    $result_t[$pmid] = 0;
                                }

                                $result_t[$pmid]++;
                            }
                        }
                    } elseif ($jflg) {
                        if ($year) {
                            if (in_array($pmid, $result_joption, true) && in_array($pmid, $result_yoption, true)) {
                                if (!isset($result_t[$pmid])) {
                                    $result_t[$pmid] = 0;
                                }

                                $result_t[$pmid]++;
                            }
                        } else {
                            if (in_array($pmid, $result_joption, true)) {
                                if (!isset($result_t[$pmid])) {
                                    $result_t[$pmid] = 0;
                                }

                                $result_t[$pmid]++;
                            }
                        }
                    } elseif ($year) {
                        if (in_array($pmid, $result_yoption, true)) {
                            if (!isset($result_t[$pmid])) {
                                $result_t[$pmid] = 0;
                            }

                            $result_t[$pmid]++;
                        }
                    } else {
                        if (!isset($result_t[$pmid])) {
                            $result_t[$pmid] = 0;
                        }

                        $result_t[$pmid]++;
                    }
                }
            }
        }

        if (!empty($a)) {
            ## Abstract

            $sql = 'select * from ' . $xoopsDB->prefix('pmid_id') . " where Abstract like '%" . $keyword[$i] . "%'" . $u_where;

            $res = $xoopsDB->query($sql);

            if ($xoopsDB->getRowsNum($res)) {
                while (false !== ($row = $xoopsDB->fetchArray($res))) {
                    $pmid = $row['id'];

                    if ($author) {
                        if ($jflg && $year) {
                            if (in_array($pmid, $result_aoption, true) && in_array($pmid, $result_joption, true) && in_array($pmid, $result_yoption, true)) {
                                if (!isset($result_a[$pmid])) {
                                    $result_a[$pmid] = 0;
                                }

                                $result_a[$pmid]++;
                            }
                        } elseif ($jflg) {
                            if (in_array($pmid, $result_aoption, true) && in_array($pmid, $result_joption, true)) {
                                if (!isset($result_a[$pmid])) {
                                    $result_a[$pmid] = 0;
                                }

                                $result_a[$pmid]++;
                            }
                        } elseif ($year) {
                            if (in_array($pmid, $result_aoption, true) && in_array($pmid, $result_yoption, true)) {
                                if (!isset($result_a[$pmid])) {
                                    $result_a[$pmid] = 0;
                                }

                                $result_a[$pmid]++;
                            }
                        } else {
                            if (in_array($pmid, $result_aoption, true)) {
                                if (!isset($result_a[$pmid])) {
                                    $result_a[$pmid] = 0;
                                }

                                $result_a[$pmid]++;
                            }
                        }
                    } elseif ($jflg) {
                        if ($year) {
                            if (in_array($pmid, $result_joption, true) && in_array($pmid, $result_yoption, true)) {
                                if (!isset($result_a[$pmid])) {
                                    $result_a[$pmid] = 0;
                                }

                                $result_a[$pmid]++;
                            }
                        } else {
                            if (in_array($pmid, $result_joption, true)) {
                                if (!isset($result_a[$pmid])) {
                                    $result_a[$pmid] = 0;
                                }

                                $result_a[$pmid]++;
                            }
                        }
                    } elseif ($year) {
                        if (in_array($pmid, $result_yoption, true)) {
                            if (!isset($result_a[$pmid])) {
                                $result_a[$pmid] = 0;
                            }

                            $result_a[$pmid]++;
                        }
                    } else {
                        if (!isset($result_a[$pmid])) {
                            $result_a[$pmid] = 0;
                        }

                        $result_a[$pmid]++;
                    }
                }
            }
        }
    }

    ## AND検索

    if ('and' == $mt) {
        foreach ($result_t as $k => $v) {
            if ($v >= $key_num) {
                if (!in_array($k, $result, true)) {
                    $result[] = $k;
                }
            }
        }

        foreach ($result_a as $k => $v) {
            if ($v >= $key_num) {
                if (!in_array($k, $result, true)) {
                    $result[] = $k;
                }
            }
        }

        ## OR検索
    } else {
        foreach ($result_t as $k => $v) {
            if (!in_array($k, $result, true)) {
                $result[] = $k;
            }
        }

        foreach ($result_a as $key => $v) {
            if (!in_array($k, $result, true)) {
                $result[] = $k;
            }
        }
    }

    ## 結果表示

    if (count($result)) {
        # １日以上前の検索結果を排除

        $time = time();

        $del = $time - 86400;

        $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_tmp') . " WHERE tmp1='lsearch' AND tmp2 < '" . $del . "'";

        $res = $xoopsDB->queryF($sql);

        # 検索結果をＤＢに登録

        $r = '';

        for ($i = 0, $iMax = count($result); $i < $iMax; $i++) {
            $r .= $result[$i] . ',';
        }

        $r = mb_substr($r, 0, -1);

        $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_tmp') . " VALUES('lsearch','" . $time . "','" . $r . "','')";

        $res = $xoopsDB->queryF($sql);

        showResult($user, $time, [$t, $a, $y, $yfrom, $yto, $j, $journal, $key, $mt, $auth, $aname, $u, $uid], 0);
    } else {
        echo "<div class='pt'>No Hit</div>";
    }
}

# ページング用関数
function showResult($user, $time, $info, $page)
{
    global $xoopsDB;

    require __DIR__ . '/class/description.php';

    $desc = new description();

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp') . " WHERE tmp1='lsearch' AND tmp2='" . $time . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $item = explode(',', $row['tmp3']);

    $num = count($item);

    echo "<div class='pt'>$num Hit</div>";

    # search info

    $si = 'time=' . $time;

    $si .= '&t=' . $info[0] . '&a=' . $info[1] . '&y=' . $info[2] . '&yfrom=' . $info[3] . '&yto=' . $info[4] . '&j=' . $info[5];

    $si .= '&journal=' . $info[6] . '&key=' . $info[7] . '&mt=' . $info[8] . '&auth=' . $info[9] . '&aname=' . $info[10] . '&u=' . $info[11] . '&uid=' . $info[12];

    $lim = 20;

    $page = $page / $lim + 1;

    $st = ($page - 1) * $lim + 1;

    require_once XOOPS_ROOT_PATH . '/class/pagenav.php';

    $xp = new XoopsPageNav($num, $lim, $st, 'p', $si);

    $show = [];

    $start = ($page - 1) * $lim;

    for ($i = $start; $i < ($start + $lim); $i++) {
        if ($i < $num) {
            $show[] = $item[$i];
        }
    }

    echo "<div style='margin-bottom:10px'>" . $xp->renderNav() . '</div>';

    echo $desc->getPaper($show, PDFDIR2);

    echo "<div style='margin-top:10px'>" . $xp->renderNav() . '</div>';
}

require XOOPS_ROOT_PATH . '/footer.php';
