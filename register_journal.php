<?php

## ------------------------
## Journal register script
## ------------------------

include 'nheader.php';
include 'include/db.php';

#register/edit
if (isset($_POST['new_reg']) || isset($_POST['edit_reg'])) {
    $name_e = $myts->stripSlashesGPC($_POST['name_e']);

    $error = '';

    if (preg_match('^[^_A-Za-z0-9]+$', $name_e)) {
        $error = 'You can only use alphabet in English form.';
    } elseif (!mb_strlen($name_e)) {
        $error = 'You have to fill in the English name form.';
    }

    if ($error) {
        redirect_header(MOD_URL . '/register_journal.php', 2, $error);
    }

    $data = [];

    $data4show = [];

    $data['j'] = $myts->stripSlashesGPC($_POST['name_e']);

    $data4show['j'] = htmlspecialchars($data['j'], 0);

    $data['j_jp'] = $myts->stripSlashesGPC($_POST['name_o']);

    $data4show['j_jp'] = htmlspecialchars($data['j_jp'], 0);

    $data['j_url'] = $myts->stripSlashesGPC($_POST['url']);

    $data4show['j_url'] = htmlspecialchars($data['j_url'], 0);

    $data['e'] = $myts->stripSlashesGPC($_POST['editor']);

    $data4show['e'] = htmlspecialchars($data['e'], 0);

    $data['pub'] = $myts->stripSlashesGPC($_POST['publisher']);

    $data4show['pub'] = htmlspecialchars($data['pub'], 0);

    # if this data is book -> 1

    (isset($_POST['book'])) ? $data['bk'] = '1' : $data['bk'] = '0';

    if (isset($_POST['new_reg'])) {
        REG_JOURNAL($data);
    } else {
        $data['id'] = (int)$_POST['id'];

        EDIT_JOURNAL($data);
    }

    require XOOPS_ROOT_PATH . '/header.php';

    include 'style.php';

    echo "<div class='pt'>" . _MD_REG_RESULT . '</div>';

    echo '<table>';

    echo "<tr><td class='head' style='width:180px'>Name in English</td><td class='even'>" . $data4show['j'] . '</td></tr>';

    echo "<tr><td class='head'>Name in other Language</td><td class='even'>" . $data4show['j_jp'] . '</td></tr>';

    echo "<tr><td class='head'>Website</td><td class='even'>" . $data4show['j_url'] . '</td></tr>';

    echo "<tr><td class='head'>Editors</td><td class='even'>" . $data4show['e'] . '</td></tr>';

    echo "<tr><td class='head'>Publisher</td><td class='even'>" . $data4show['pub'] . '</td></tr>';

    echo '</table>';

    echo "<a href='" . MOD_URL . "/register_journal.php'>Back</a>";

    require XOOPS_ROOT_PATH . '/footer.php';

#remove
} elseif (isset($_GET['rem'])) {
    $id = (int)$_GET['rem'];

    $mes = '';

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Journal='" . $id . "'";

    $res = $xoopsDB->query($sql);

    if (!$xoopsDB->getRowsNum($res)) {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $id . "'";

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res)) {
            $sql = 'DELETE FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $id . "'";

            $res = $xoopsDB->queryF($sql);

            if ($res) {
                $mes = _MD_DELETEDJ;
            }
        }
    }

    if (!$mes) {
        $mes = _MD_DELETEDJERROR;
    }

    redirect_header(MOD_URL . '/register_journal.php', 2, $mes);

#register form
} else {
    #edit mode

    if (isset($_GET['j_edit'])) {
        $id = (int)$_GET['j_edit'];

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_journal') . " WHERE id='" . $id . "'";

        $res = $xoopsDB->query($sql);

        if ($xoopsDB->getRowsNum($res)) {
            $row = $xoopsDB->fetchArray($res);

            $data = [];

            $data['id'] = $id;

            $data['j'] = htmlspecialchars($row['Journal'], 0);

            $data['j_jp'] = htmlspecialchars($row['Journal_JP'], 0);

            $data['j_url'] = htmlspecialchars($row['URL'], 0);

            $data['bk'] = $row['Book'];

            $data['e'] = htmlspecialchars($row['Editor'], 0);

            $data['pub'] = htmlspecialchars($row['Publisher'], 0);

            require XOOPS_ROOT_PATH . '/header.php';

            include 'style.php';

            echo "<div class='pt'>" . _MD_JOUR_EDIT . '</div>';

            JREGISTER_FORM($data);

            require XOOPS_ROOT_PATH . '/footer.php';
        } else {
            redirect_header(MOD_URL . '/register_journal.php', 2, _MD_DOSENTEXIST);
        }

        #register mode
    } else {
        require XOOPS_ROOT_PATH . '/header.php';

        include 'style.php';

        echo "<div class='pt'>" . _MD_JOUR_REG . '</div>';

        JREGISTER_FORM();

        echo "<div class='pt'>" . _MD_JOUR_EDIT . '</div>';

        #journal initial

        $link = [];

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_journal') . ' ORDER BY Journal';

        $res = $xoopsDB->query($sql);

        while (false !== ($row = $xoopsDB->fetchArray($res))) {
            if ('(unknown)' == $row['Journal']) {
                continue;
            }

            $i = mb_strtoupper(mb_substr($row['Journal'], 0, 1));

            if (!in_array($i, $link, true)) {
                $link[] = $i;
            }
        }

        for ($i = 0, $iMax = count($link); $i < $iMax; $i++) {
            echo "<a href='#" . $link[$i] . "'>" . $link[$i] . '</a> ';
        }

        echo '<br><br>';

        #journal list

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_journal') . ' ORDER BY Journal';

        $res = $xoopsDB->query($sql);

        $pre_index = '';

        echo "<table style='border:0 0 1px 0 solid #ebebeb' rules='rows'>";

        while (false !== ($row = $xoopsDB->fetchArray($res))) {
            $j = $row['Journal'];

            if ('(unknown)' == $j) {
                continue;
            }

            $j_jp = $row['Journal_JP'];

            ($j_jp) ? $j_jp = '/' . $j_jp : $j_jp = '';

            $url = $row['URL'];

            $id = $row['id'];

            #index

            $index = mb_strtoupper(mb_substr($j, 0, 1));

            if ($index != $pre_index) {
                echo "<tr class='even'><td colspan='2'>";

                echo "<a name='" . $index . "'>" . $index . '</a></td></tr>';
            }

            $pre_index = $index;

            $j = htmlspecialchars($j, 0);

            $j_jp = htmlspecialchars($j_jp, 0);

            $url = htmlspecialchars($url, 0);

            echo '<tr><td>' . $j . $j_jp . '</td>';

            echo "<td style='width:20%; text-align:right'>";

            #url edit remove

            if ($url) {
                echo "<a href='" . $url . "' target='_blank'>web</a>&nbsp;&nbsp;";
            }

            echo "<a href='register_journal.php?j_edit=" . $id . "'>edit</a>&nbsp;&nbsp;";

            $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE Journal='" . $id . "'";

            $res2 = $xoopsDB->query($sql);

            if (!$xoopsDB->getRowsNum($res2)) {
                echo "<a href='register_journal.php?rem=" . $id . "'>remove</a> ";
            }

            echo '</td></tr>';
        }

        echo '</table>';

        require XOOPS_ROOT_PATH . '/footer.php';
    }
}

function JREGISTER_FORM($data = '')
{
    if ('' == $data) {
        $data = ['j' => '', 'j_jp' => '', 'j_url' => '', 'e' => '', 'pub' => '', 'bk' => '', 'id' => ''];
    }

    echo "<form action='register_journal.php' method='POST' style='margin:20px 10px 20px 0'>";

    echo '<table>';

    echo "<tr><td style='width:200px'><b>*Name in English</b></td>";

    echo "<td><input type='text' name='name_e' style='width:90%' value='" . $data['j'] . "'></td></tr>";

    echo '<tr><td><b> Name in other Language</b></td>';

    echo "<td><input type='text' name='name_o' style='width:90%' value='" . $data['j_jp'] . "'></td></tr>";

    echo '<tr><td><b> Website</b></td>';

    echo "<td><input type='text' name='url' style='width:90%' value='" . $data['j_url'] . "'></td></tr>";

    echo '<tr><td><b>Editors</b></td>';

    echo "<td><input type='text' style='width:90%' name='editor' value='" . $data['e'] . "'></td>";

    echo '</tr>';

    echo '<tr><td><b>Publisher</b></td>';

    echo "<td><input type='text' style='width:90%' name='publisher' value='" . $data['pub'] . "'></td>";

    echo '</tr>';

    echo '<tr><td><b> Is this Book?</b></td>';

    echo "<td><input type='checkbox' name='book' value='y'";

    if (1 == $data['bk']) {
        echo ' checked';
    }

    echo '>YES</td></tr>';

    echo '<tr><td> </td>';

    if ($data['id']) {
        echo "<td><input type='submit' name='edit_reg' value='submit'></td></tr>";

        echo "<tr><input type='hidden' name='id' value='" . $data['id'] . "'></tr>";
    } else {
        echo "<td><input type='submit' name='new_reg' value='submit'></td></tr>";
    }

    echo '</table>';

    echo '</form>';
}
