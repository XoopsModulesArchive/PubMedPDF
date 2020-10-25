<?php

if (version_compare(PHP_VERSION, '5', '>=')) {
    require_once __DIR__ . '/domxml-php4-to-php5.php';
}

# PubMed($PMID, $proxy, $snoopy, $xml_dir )
#-----------------------------------------------------------
# PubMedサイトからデータを取得する
#
# 引数：PubMedID, array[proxy_yesno,proxy_url,proxy_port]
#
# 戻り値：PMIDが存在しなければ 0
#         PMIDが存在すれば論文情報の格納された配列
#         array[id,j,y,v,p,ab,t,a]
#         (array[id,journal,year,volume,page,abstract,title,author])
#------------------------------------------------------------

function PubMed($PMID, $proxy, $snoopy, $xml_dir)
{
    $PMID = rtrim($PMID);

    $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils';

    # Do PubMed search

    $esearch = $url . '/esearch.fcgi?db=Pubmed&retmax=1&usehistory=y&term=' . $PMID;

    $pubmed_xml = '';

    # Proxy mode

    if ($proxy) {
        $proxy->fetch($esearch);

        $pubmed_xml = $proxy->results;

    # non Proxy mode
    } else {
        // change to Snoopy by H.Ikeno 2005/04/19

        $snoopy->fetch($esearch);

        $pubmed_xml = $snoopy->results;
    }

    $dom = domxml_open_mem($pubmed_xml);

    # Count

    $array = $dom->get_elements_by_tagname('Count');

    $count = $array[0]->get_content();

    if ('1' != $count) {
        return 0;
    }

    # Query Key

    $array = $dom->get_elements_by_tagname('QueryKey');

    $query_key = $array[0]->get_content();

    # WebEnv

    $array = $dom->get_elements_by_tagname('WebEnv');

    $webenv = $array[0]->get_content();

    # Do PubMed fetch

    $efetch = $url . '/efetch.fcgi?rettype=XML&retmode=text&db=Pubmed&query_key=' . $query_key . '&WebEnv=' . $webenv;

    $pubmed_xml = '';

    # Proxy mode

    if ($proxy) {
        $proxy->fetch($efetch);

        $pubmed_xml = $proxy->results;

    # non Proxy mode
    } else {
        // change to Snoopy by H.Ikeno 2005/04/19

        $snoopy->fetch($efetch);

        $pubmed_xml = $snoopy->results;
    }

    if (!is_dir('pubmed_xml/')) {
        $rc = mkdir('pubmed_xml/', 0777);

        if (!$rc) {
            echo 'mkdir [ pubmed_xml ] false';
        }
    }

    // save XML file

    if (mb_strlen($xml_dir)) {
        if ('/' != mb_substr($xml_dir, -1)) {
            $xml_dir .= '/';
        }

        $pass = $xml_dir . $PMID . '.xml';

        $file = fopen($pass, 'wb');

        fwrite($file, $pubmed_xml);

        fclose($file);
    }

    $pm_data = parseXML($PMID, $pubmed_xml);

    return $pm_data;
}

# PubMedByFile($PMID, $xml_dir)
#-----------------------------------------------------------
# XMLファイルから情報を抽出する
#
# 引数：$PMID, $xml_dir
#       $PMID: PubMedID
#       $xml_dir: XMLファイルのディレクトリ,
#
# 戻り値：PMIDが存在しなければ 0
#         PMIDが存在すれば論文情報の格納された配列
#         array[id,j,y,v,p,ab,t,a]
#         (array[id,journal,year,volume,page,abstract,title,author])
#------------------------------------------------------------

function PubMedByFile($PMID, $xml_dir)
{
    if ('/' != mb_substr($xml_dir, -1)) {
        $xml_dir .= '/';
    }

    $file_name = $xml_dir . $PMID . '.xml';

    $pm_data = [];

    if (file_exists($file_name)) {
        $pubmed_xml = '';

        $file = fopen($file_name, 'rb');

        while (!feof($file)) {
            $pubmed_xml .= fgets($file, 1000);
        }

        fclose($file);

        $pm_data = parseXML($PMID, $pubmed_xml);
    }

    return $pm_data;
}

# PubMedKWSearch($keywords, $proxy, $snoopy, $retmax, $retstart)
#-----------------------------------------------------------
# キーワードによりPubMedに対して検索をかける
#
# 引数：$keywords, $proxy, $retmax, $retstart
#       $keywords: キーワード
#       $proxy: プロキシ情報
#	$snoopy: snoopyインスタンス
#	$retmax: 検索件数
#	$retstart: 検索開始番号
#
# 戻り値：検索結果
#         array[n,r,s,p]
#         (array[total,numberPerPage,startNumber,pmid])
#------------------------------------------------------------

function PubMedKWSearch($keywords, $proxy, $snoopy, $retmax, $retstart)
{
    $keywords = rtrim($keywords);

    $url = 'http://eutils.ncbi.nlm.nih.gov/entrez/eutils';

    # Do PubMed search

    $startNo = $retstart * $retmax;

    $esearch = $url . '/esearch.fcgi?db=Pubmed&usehistory=y&retmode=xml&' . "retmax=$retmax&retstart=$retstart&term=" . $keywords;

    # Proxy mode

    if ($proxy) {
        $proxy->fetch($esearch);

        $pubmed_xml = $proxy->results;

    # non Proxy mode
    } else {
        // change to Snoopy by H.Ikeno 2005/04/18

        $snoopy->fetch($esearch);

        $pubmed_xml = $snoopy->results;
    }

    #special char replace

    $pubmed_xml = special_char_conv($pubmed_xml);

    # Analysis xml data

    $pm_data = [];

    $dom = domxml_open_mem($pubmed_xml);

    $ctx = xpath_new_context($dom);

    $xpath_base = '/eSearchResult';

    # Number of contents

    $xpath = $xpath_base . '/Count';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $count = $node->get_content();
    } else {
        $count = 0;
    }

    $pm_data['n'] = $count;

    # Number of contents in this page

    $xpath = $xpath_base . '/RetMax';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $retmax = $node->get_content();
    } else {
        $retmax = 0;
    }

    $pm_data['r'] = $retmax;

    # Start number of contents in this page

    $xpath = $xpath_base . '/RetStart';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $retstart = $node->get_content();
    } else {
        $retstart = 0;
    }

    $pm_data['s'] = $retstart;

    # PMID

    $array = $dom->get_elements_by_tagname('Id');

    $pmid = '';

    if (isset($array[0])) {
        for ($i = 0, $iMax = count($array); $i < $iMax; $i++) {
            $parent = $array[$i]->parent_node();

            $array2 = $parent->get_elements_by_tagname('Id');

            if (0 != $i) {
                $pmid .= ',';
            }

            $pmid .= $array2[$i]->get_content();
        }
    } else {
        $pmid = '';
    }

    $pm_data['p'] = $pmid;

    /*
        # Search term
        $array = $dom->get_elements_by_tagname("Term");

        $term = "";
        if($array[0]){
            for($i=0; $i<count($array); $i++){
                $parent = $array[$i]->parent_node();
                $array2 = $parent->get_elements_by_tagname("Term");
                if ( $i != 0 ) $term .= ",";
                $word = $array2[$i]->get_content();
                $word = str_replace( '[', '/', $word );
                $word = str_replace( ']', '', $word );
                $term .= $word;
            }
        }else{
            $term = "";
        }
        $pm_data[t] = $term;
    */

    return $pm_data;
}

#-------------------------------------------------------#

function parseXML($PMID, $pubmed_xml)
{
    #special char replace

    $pubmed_xml = special_char_conv($pubmed_xml);

    # Analysis xml data

    $pm_data = [];

    $pm_data['id'] = $PMID;

    $dom = domxml_open_mem($pubmed_xml);

    $ctx = xpath_new_context($dom);

    $xpath_base = '/PubmedArticleSet/PubmedArticle/MedlineCitation';

    # Journal

    $xpath = $xpath_base . '/MedlineJournalInfo/MedlineTA';

    $x_array = $ctx->xpath_eval($xpath);

    if (isset($x_array->nodeset[0])) {
        $node = $x_array->nodeset[0];

        $Journal = $node->get_content();

        $Journal = mb_convert_encoding($Journal, 'euc-jp', 'UTF-8');
    } else {
        $Journal = '- unknown - ';
    }

    $pm_data['j'] = $Journal;

    # Year

    $xpath = $xpath_base . '/Article/Journal/JournalIssue/PubDate/Year';

    $x_array = $ctx->xpath_eval($xpath);

    if (isset($x_array->nodeset[0])) {
        $node = $x_array->nodeset[0];

        $Year = $node->get_content();

        $Year = mb_convert_encoding($Year, 'euc-jp', 'UTF-8');
    } else {
        $xpath = $xpath_base . '/Article/Journal/JournalIssue/PubDate/MedlineDate';

        $x_array = $ctx->xpath_eval($xpath);

        if (isset($x_array->nodeset[0])) {
            $node = $x_array->nodeset[0];

            $Year = $node->get_content();

            $Year = mb_convert_encoding($Year, 'euc-jp', 'UTF-8');

            $Year = mb_substr($Year, 0, 4);
        } else {
            $Year = '0000';
        }
    }

    $pm_data['y'] = $Year;

    # Volume

    $xpath = $xpath_base . '/Article/Journal/JournalIssue/Volume';

    $x_array = $ctx->xpath_eval($xpath);

    if (isset($x_array->nodeset[0])) {
        $node = $x_array->nodeset[0];

        $Volume = $node->get_content();

        $Volume = mb_convert_encoding($Volume, 'euc-jp', 'UTF-8');
    } else {
        $Volume = '';
    }

    $pm_data['v'] = $Volume;

    # Page

    $xpath = $xpath_base . '/Article/Pagination/MedlinePgn';

    $x_array = $ctx->xpath_eval($xpath);

    if (isset($x_array->nodeset[0])) {
        $node = $x_array->nodeset[0];

        $Page = $node->get_content();

        $Page = mb_convert_encoding($Page, 'euc-jp', 'UTF-8');
    } else {
        $Page = '';
    }

    $pm_data['p'] = $Page;

    # Abstract

    $array = $dom->get_elements_by_tagname('AbstractText');

    if (isset($array[0])) {
        $Abst = $array[0]->get_content();

        $Abst = mb_convert_encoding($Abst, 'euc-jp', 'UTF-8');
    } else {
        $Abst = '';
    }

    $pm_data['ab'] = $Abst;

    # Title

    $array = $dom->get_elements_by_tagname('ArticleTitle');

    if ($array[0]) {
        $Title = $array[0]->get_content();

        $Title = mb_convert_encoding($Title, 'euc-jp', 'UTF-8');
    } else {
        $Title = '- unknown -';
    }

    $pm_data['t'] = $Title;

    # Authors

    $array = $dom->get_elements_by_tagname('Author');

    $Authors = '';

    if ($array[0]) {
        for ($i = 0, $iMax = count($array); $i < $iMax; $i++) {
            $parent = $array[$i]->parent_node();

            $array2 = $parent->get_elements_by_tagname('LastName');

            if ($array2[$i]) {
                $LastName = $array2[$i]->get_content();

                $array2 = $parent->get_elements_by_tagname('Initials');

                if ($array2[$i]) {
                    $Initials = $array2[$i]->get_content();
                } else {
                    $array2 = $parent->get_elements_by_tagname('ForeName');

                    if ($array2[$i]) {
                        $Initials = $array2[$i]->get_content();
                    }
                }

                $Authors .= $LastName . ' ' . $Initials . ', ';

                $Authors = mb_convert_encoding($Authors, 'euc-jp', 'UTF-8');
            }
        }

        $Authors = mb_substr($Authors, 0, -2);
    } else {
        $Authors = '';
    }

    $pm_data['a'] = $Authors;

    return $pm_data;
}

function special_char_conv($str)
{
    require_once __DIR__ . '/class/functions.php';

    $fn = new functions();

    return $fn->special_char_conv($str);
}
