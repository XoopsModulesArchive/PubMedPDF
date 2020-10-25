<?php

$modversion['name'] = 'PubMed PDF';
$modversion['version'] = '1.5';
$modversion['description'] = '';
$modversion['credits'] = 'Nishioka T, Ikeno H, Kanai R';
$modversion['author'] = 'Nishioka T, Ikeno H, Kanai R';
$modversion['official'] = 0;
$modversion['image'] = 'images/logo.png';
$modversion['dirname'] = 'PubMedPDF';

//SQL
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables']['0'] = 'pmid_id';
$modversion['tables']['1'] = 'pmid_journal';
$modversion['tables']['2'] = 'pmid_author';
$modversion['tables']['3'] = 'pmid_favorite_dir';
$modversion['tables']['4'] = 'pmid_favorite_data';
$modversion['tables']['5'] = 'pmid_template';
$modversion['tables']['6'] = 'pmid_shortcut';
$modversion['tables']['7'] = 'pmid_memo';
$modversion['tables']['8'] = 'pmid_tmp';

//Menu
$modversion['hasMain'] = 1;
$modversion['sub'][1]['name'] = _MI_REGISTER;
$modversion['sub'][1]['url'] = 'register.php';
$modversion['sub'][2]['name'] = _MI_REGISTER2;
$modversion['sub'][2]['url'] = 'register_wopmid.php';
$modversion['sub'][3]['name'] = _MI_REGISTER3;
$modversion['sub'][3]['url'] = 'register_journal.php';
$modversion['sub'][4]['name'] = _MI_LOCALSEARCH;
$modversion['sub'][4]['url'] = 'local_search.php';
$modversion['sub'][5]['name'] = _MI_REGISTER4;
$modversion['sub'][5]['url'] = 'search.php';
$modversion['sub'][6]['name'] = _MI_FAVORITE;
$modversion['sub'][6]['url'] = 'favorite.php';
$modversion['sub'][7]['name'] = _MI_FAVORITEPUB;
$modversion['sub'][7]['url'] = 'favorite_pub.php';
$modversion['sub'][8]['name'] = _MI_BIBLIO;
$modversion['sub'][8]['url'] = 'biblio.php';

//Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

$modversion['config'][1]['name'] = 'updir';
$modversion['config'][1]['title'] = '_MI_UPDIR';
$modversion['config'][1]['description'] = '_MI_UPDIR_DESC';
$modversion['config'][1]['formtype'] = 'textbox';
$modversion['config'][1]['valuetype'] = 'text';
$modversion['config'][1]['default'] = 'pdf';

$modversion['config'][2]['name'] = 'updir2';
$modversion['config'][2]['title'] = '_MI_UPDIR2';
$modversion['config'][2]['description'] = '_MI_UPDIR2_DESC';
$modversion['config'][2]['formtype'] = 'textbox';
$modversion['config'][2]['valuetype'] = 'text';
$modversion['config'][2]['default'] = 'pdf';

$modversion['config'][6]['name'] = 'news';
$modversion['config'][6]['title'] = '_MI_NEWS';
$modversion['config'][6]['description'] = '_MI_NEWS_DESCR';
$modversion['config'][6]['formtype'] = 'yesno';
$modversion['config'][6]['valuetype'] = 'int';
$modversion['config'][6]['default'] = 0;

$modversion['config'][7]['name'] = 'suffix';
$modversion['config'][7]['title'] = '_MI_SUFFIX';
$modversion['config'][7]['description'] = '_MI_SUFFIX_DESCR';
$modversion['config'][7]['formtype'] = 'textbox';
$modversion['config'][7]['valuetype'] = 'text';
$modversion['config'][7]['default'] = 'zip|txt|pdf';

$modversion['config'][3]['name'] = 'proxy';
$modversion['config'][3]['title'] = '_MI_PROXY';
$modversion['config'][3]['formtype'] = 'yesno';
$modversion['config'][3]['valuetype'] = 'int';
$modversion['config'][3]['default'] = 0;

$modversion['config'][4]['name'] = 'proxy_url';
$modversion['config'][4]['title'] = '_MI_PURL';
$modversion['config'][4]['formtype'] = 'textbox';
$modversion['config'][4]['valuetype'] = 'text';
$modversion['config'][4]['default'] = '192.168.0.1';

$modversion['config'][5]['name'] = 'proxy_port';
$modversion['config'][5]['title'] = '_MI_PPORT';
$modversion['config'][5]['formtype'] = 'textbox';
$modversion['config'][5]['valuetype'] = 'int';
$modversion['config'][5]['default'] = '8080';
