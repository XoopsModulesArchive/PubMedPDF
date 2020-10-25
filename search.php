<?php

require __DIR__ . '/nheader.php';
require XOOPS_ROOT_PATH . '/class/snoopy.php';
require __DIR__ . '/class/description.php';
require __DIR__ . '/include/pubmed.php';
require __DIR__ . '/include/db.php';

require XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/style.php';

## Proxy
if ($xoopsModuleConfig['proxy']) {
    $proxy = new Snoopy();

    $proxy->read_timeout = 0;

    $proxy->proxy_host = $xoopsModuleConfig['proxy_url'];

    $proxy->proxy_port = $xoopsModuleConfig['proxy_port'];
} else {
    $proxy = 0;

    $snoopy = new Snoopy();
}

$key4show = '';
$retmax = 10;
$method = 'all';
$register = '';

if (isset($_GET['search'])) {
    $keywords = $myts->stripSlashesGPC($_GET['keywords']);

    $key4show = htmlspecialchars($keywords, ENT_QUOTES | ENT_HTML5);

    $keywords = str_replace(' ', '+', $keywords);

    $retmax = (int)$_GET['retmax'];

    $method = $_GET['mt'];

    if ('all' != $method && 'selected' != $method) {
        $method = 'all';
    }

    $page = 1;

    if (isset($_GET['p'])) {
        $page = (int)$_GET['p'];

        if ($page < 1) {
            $page = 1;
        }
    }

    $startNo = ($page - 1) * $retmax;
}

# uninterest <-> interest, register
if (isset($_POST['check_submit'])) {
    switch ($_POST['check_method']) {
        case 'uninterest':
            foreach ($_POST['uninterest'] as $pmid) {
                $pmid = (int)$pmid;

                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');

                $sql .= " WHERE tmp1='uninterest' AND tmp2='" . $user . "' AND tmp3='" . $pmid . "'";

                $res = $xoopsDB->query($sql);

                if ($xoopsDB->getRowsNum($res)) {
                    $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_tmp');

                    $sql .= " WHERE tmp1='uninterest' AND tmp2='" . $user . "' AND tmp3='" . $pmid . "'";

                    $res = $xoopsDB->query($sql);
                } else {
                    $sql = 'INSERT INTO ' . $xoopsDB->prefix('pmid_tmp');

                    $sql .= " VALUES('uninterest','" . $user . "','" . $pmid . "','')";

                    $res = $xoopsDB->query($sql);
                }
            }
            break;
        case 'register':
            $pxml = MOD_PATH . '/' . PXMLDIR;
            $sxml = MOD_PATH . '/' . SXMLDIR;
            $count = 0;
            foreach ($_POST['uninterest'] as $pmid) {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE PMID='" . $pmid . "'";

                $res = $xoopsDB->query($sql);

                if ($xoopsDB->getRowsNum($res)) {
                    continue;
                }

                if ($count > 70) {
                    for ($j = 0; $j < 10000; $j++) {
                    }

                    $count = 0;
                }

                if (file_exists($pxml . '/' . $pmid . '.xml')) {
                    if ($pm_data = PubMedByFile($pmid, $pxml)) {
                        $t = $pm_data['t'];

                        if (mb_strlen($t) > 80) {
                            $t = mb_substr($t, 0, 80) . '...';
                        }

                        $register .= '[' . $pmid . '] ' . $t . '<br>';

                        DB($pm_data, $user);
                    }
                } elseif (file_exists($sxml . '/' . $pmid . '.xml')) {
                    if ($pm_data = PubMedByFile($pmid, $sxml)) {
                        $t = $pm_data['t'];

                        if (mb_strlen($t) > 80) {
                            $t = mb_substr($t, 0, 80) . '...';
                        }

                        $register .= '[' . $pmid . '] ' . $t . '<br>';

                        DB($pm_data, $user);

                        rename($sxml . '/' . $pmid . '.xml', $pxml . '/' . $pmid . '.xml');
                    }
                } else {
                    if ($pm_data = PubMed($pmid, $proxy, $snoopy, $pxml)) {
                        $t = $pm_data['t'];

                        if (mb_strlen($t) > 80) {
                            $t = mb_substr($t, 0, 80) . '...';
                        }

                        $register .= '[' . $pmid . '] ' . $t . '<br>';

                        DB($pm_data, $user);
                    }

                    $count++;
                }
            }
            break;
    }
}

echo <<<E
	<script>
	function changeKey(){
		document.getElementById('kwf').value = document.getElementById('kw').value;
	}
	</script>
E;

echo "<form enctype='multipart/form-data' action='' method='GET'>";
echo "<div class='pt'>" . _MD_PubMedSearch . '</div>';
echo "<div class='pc'><table><tr>";
echo "<td style='width:80px'><b>keywords</b></td>";
echo "<td><input type='text' id='kwf' style='width:60%' name='keywords' value='" . $key4show . "'></td></tr>";

if ('guest' != $user) {
    echo '<tr><td><b>Your KW</b></td>';

    echo "<td><select id='kw' onchange='changeKey()'>";

    echo "<option  width='100px' value=''>Choice your keyword</option>";

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp') . " WHERE tmp1='psearch' AND tmp4='" . $user . "'";

    $res = $xoopsDB->query($sql);

    while (false !== ($row = $xoopsDB->fetchArray($res))) {
        $token = explode('::', $row['tmp3']);

        echo "<option value='" . $token[1] . "' >" . $token[0] . '</option>';
    }

    echo '</select>';

    echo "<br><a href='keywords.php'><b>" . _MD_PubMedmanage . '</b></a>';

    echo '</td></tr>';
}

echo '<tr><td><b>Number</b></td><td>';
$ret_array = [10, 20, 50, 100, 200];
for ($i = 0, $iMax = count($ret_array); $i < $iMax; $i++) {
    echo "<input type='radio' name='retmax' value='" . $ret_array[$i] . "'";

    if ($retmax == $ret_array[$i]) {
        echo ' checked';
    }

    echo '>' . $ret_array[$i] . ' ';
}
echo '</td></tr>';

echo '<tr><td><b>Display</b></td><td>';
if ('selected' == $method) {
    echo "<input type='radio' name='mt' value='all'>All";

    echo "<input type='radio' name='mt' value='selected' checked>Selected";
} else {
    echo "<input type='radio' name='mt' value='all' checked>All";

    echo "<input type='radio' name='mt' value='selected'>Selected";
}
echo '</td></tr>';

echo '<tr><td><b>Search</b></td><td>';
echo "<input type='submit' name='search' value='submit'>";
echo '</td></tr></table></div></form>';

## search result
if (isset($_GET['search'])) {
    ## PubMed Search & Fetch

    $pm_data = PubMedKWSearch($keywords, $proxy, $snoopy, $retmax, $startNo);

    if (0 == $pm_data['n']) {
        echo 'Search condition: No items found.<br>';
    } else {
        # paging

        $num = $pm_data['n'];

        $allpage = ceil($pm_data['n'] / $retmax);

        $prepage = $page - 1;

        if ($prepage < 1) {
            $prepage = 1;
        }

        $nextpage = $page + 1;

        $keywords = str_replace('+', ' ', $keywords);

        echo "<form action='' method='GET' style='margin:0'>";

        echo '<hr><b>Total:</b> ' . $pm_data['n'] . ' &nbsp;&nbsp;&nbsp;&nbsp;';

        $get = '&keywords=' . $keywords . '&retmax=' . $retmax . '&mt=' . $method . '&search=y';

        if ($page > 1) {
            echo "<a href='search.php?p=" . $prepage . '&' . $get . "'>";

            echo "<img src='images/left.png'></a> ";
        }

        echo $page . ' / ' . $allpage;

        if (($nextpage - 1) * $retmax < $num) {
            echo "<a href='search.php?p=" . $nextpage . '&' . $get . "'>";

            echo " <img src='images/right.png'></a>";
        }

        echo '&nbsp;&nbsp;&nbsp;&nbsp;<b>Move to the page</b>';

        echo " <select name='p'>";

        for ($i = 1; $i <= $allpage; $i++) {
            echo "<option value='" . $i . "'>" . $i . '</option>';
        }

        echo '</select> ';

        echo "<input type='submit' value='Go' name='search'>";

        echo "<input type='hidden' value='" . $keywords . "' name='keywords'>";

        echo "<input type='hidden' value='" . $retmax . "' name='retmax'>";

        echo "<input type='hidden' value='" . $method . "' name='mt'>";

        echo '<hr><br></form>';

        if (!empty($register)) {
            echo 'Registered<br>' . $register . '<hr>';
        }

        # show data

        echo "<form action='?p=" . $page . '&' . $get . "' method='POST'>";

        $pmids = explode(',', $pm_data['p']);

        $desc = new description($user);

        foreach ($pmids as $pmid) {
            # registered data

            if ($data = $desc->getPaperInfo($pmid, 'pmid')) {
                if ('all' == $method) {
                    echo $desc->getPaper4PS($data, PDFDIR2, 'reg', 'black');
                }

                # non registered data
            } else {
                $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_tmp');

                $sql .= " WHERE tmp1='uninterest' AND tmp2='" . $user . "' AND tmp3='" . $pmid . "'";

                $res = $xoopsDB->query($sql);

                if ($xoopsDB->getRowsNum($res)) {
                    if ('selected' == $method) {
                        continue;
                    }

                    $color = 'blue';
                } else {
                    $color = 'red';
                }

                # get infomation

                $search_xml = MOD_PATH . '/' . SXMLDIR;

                if (file_exists($search_xml . '/' . $pmid . '.xml')) {
                    $pm_data = PubMedByFile($pmid, $search_xml);
                } else {
                    $pm_data = PubMed($pmid, $proxy, $snoopy, $search_xml);
                }

                $data = [];

                $data['pmid'] = $pm_data['id'];

                $data['journal'] = $pm_data['j'];

                $data['year'] = $pm_data['y'];

                $data['v'] = $pm_data['v'];

                $data['abst'] = $pm_data['ab'];

                $data['author'] = $pm_data['a'];

                $data['title'] = $pm_data['t'];

                $data['pp'] = $pm_data['p'];

                echo $desc->getPaper4PS($data, PDFDIR2, 'nonreg', $color);
            }
        }

        echo '<hr><br>';

        echo "<input type='radio' name='check_method' value='uninterest' checked> " . _MD_PubMedSearch_unin_submit . '<br>';

        echo "<input type='radio' name='check_method' value='register'> " . _MD_PubMedSearch_regist_submit . '<br><br>';

        echo "<input type='submit' value='submit' name='check_submit'>";

        echo '</form>';
    }
}

require XOOPS_ROOT_PATH . '/footer.php';
