<?php

require __DIR__ . '/nheader.php';
require XOOPS_ROOT_PATH . '/class/snoopy.php';

if (1 != $isadmin) {
    redirect_header(MOD_URL, 2, _MD_NOTPERMITTED);
}

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

require XOOPS_ROOT_PATH . '/header.php';
Auto_run(PDFDIR, $proxy, $snoopy, $user, MOD_PATH . '/' . PXMLDIR, MOD_PATH . '/' . SXMLDIR);
require XOOPS_ROOT_PATH . '/footer.php';

function Auto_run($dir, $proxy, $snoopy, $user, $pxml, $sxml)
{
    global $xoopsDB;

    require 'include/pubmed.php';

    require 'include/db.php';

    $count = 0;

    if ($handle = opendir($dir)) {
        while (false !== $file = readdir($handle)) {
            if ('.' != $file && '..' != $file) {
                if (preg_match("/.*(\.pdf)$/i", $file)) {
                    $PMID = (int)mb_substr($file, 0, -4);

                    #check PMID

                    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('pmid_id') . " WHERE PMID='" . $PMID . "'";

                    $res = $xoopsDB->query($sql);

                    if (!$xoopsDB->getRowsNum($res)) {
                        if ($count > 70) {
                            for ($i = 0; $i < 10000; $i++) {
                            }

                            $count = 0;
                        }

                        if (file_exists($pxml . '/' . $PMID . '.xml')) {
                            if ($pm_data = PubMedByFile($PMID, $pxml)) {
                                echo ' - ' . $pm_data['t'] . '<br>';

                                DB($pm_data, $user);
                            }
                        } elseif (file_exists($sxml . '/' . $PMID . '.xml')) {
                            if ($pm_data = PubMedByFile($PMID, $sxml)) {
                                echo ' - ' . $pm_data['t'] . '<br>';

                                DB($pm_data, $user);

                                rename($sxml . '/' . $PMID . '.xml', $pxml . '/' . $PMID . '.xml');
                            }
                        } else {
                            if ($pm_data = PubMed($PMID, $proxy, $snoopy, $pxml)) {
                                echo ' - ' . $pm_data['t'] . '<br>';

                                DB($pm_data, $user);
                            }

                            $count++;
                        }
                    }
                }
            }
        }

        closedir($handle);
    }
}
