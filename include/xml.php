<?php

if (version_compare(PHP_VERSION, '5', '>=')) {
    require_once MOD_PATH . '/include/domxml-php4-to-php5.php';
}

# GENERATE_XML()
#------------------------------------------------------------------
# Generating XML file for registered bibliography
#
# arguments¡§
#-------------------------------------------------------------------

function GENERATE_XML($data)
{
    global $xoopsDB;

    $t = $data['t'];            // Title
    $t_jp = $data['t_jp'];    // Title(jp)
    $y = $data['y'];            // Year
    $a = $data['a'];            // Authors
    $a_jp = $data['a_jp'];    // Authors(jp)
    $j = $data['j'];            // Journal
    $jb = $data['jb'];        // Journal or Book
    $v = $data['v'];            // Volume
    $p = $data['p'];            // Pages
    $ab = $data['ab'];        // Abstract
    $c_t1 = $data['c_t1'];    // ID text

    $sql = 'select * from ' . $xoopsDB->prefix('pmid_journal') . " where Journal='" . $j . "'";

    $res = $xoopsDB->query($sql);

    $row = $xoopsDB->fetchArray($res);

    $j_JP = $row['Journal_JP'];

    $dir = MOD_PATH . '/' . UPXML;

    $file_name = $c_t1 . '.xml';

    $file = fopen($dir . '/' . $file_name, 'wb');

    $fileDTD = fopen(MOD_PATH . '/include/PubmedPDF.dtd', 'rb');

    while (!feof($fileDTD)) {
        $lineDTD = fgets($fileDTD, 1000);

        fwrite($file, $lineDTD);
    }

    // start to generate XML file

    fwrite($file, "<ppdfArticleSet>\n");

    fwrite($file, "<ppdfArticle>\n");

    // Index

    fwrite($file, '<ppdfIndex>');

    fwrite($file, $c_t1);

    fwrite($file, '</ppdfIndex>');

    // Date

    $date_array = getdate();

    fwrite($file, "<DateCreated>\n");

    fwrite($file, '<Year>' . $date_array['year'] . '</Year>');

    fwrite($file, '<Month>' . $date_array['mon'] . '</Month>');

    fwrite($file, '<Day>' . $date_array['mday'] . '</Day>');

    fwrite($file, "</DateCreated>\n");

    fwrite($file, '<Article PubModel="Print">');

    // Journal

    fwrite($file, "<Journal>\n");

    fwrite($file, "<JournalIssue PrintYN=\"Y\">\n");

    fwrite($file, "<Volume>$v</Volume>\n");

    fwrite($file, "<PubDate>\n");

    fwrite($file, "<Year>$y</Year>\n");

    fwrite($file, "</PubDate>\n");

    fwrite($file, "</JournalIssue>\n");

    fwrite($file, "</Journal>\n");

    // Article

    fwrite($file, "<ArticleTitle>$t</ArticleTitle>\n");

    fwrite($file, "<ArticleTitleJP>$t_jp</ArticleTitleJP>\n");

    fwrite($file, "<Pagination>\n");

    fwrite($file, "<MedlinePgn>$p</MedlinePgn>\n");

    fwrite($file, "</Pagination>\n");

    fwrite($file, "<Abstract>$ab</Abstract>\n");

    // Author

    fwrite($file, "<AuthorList CompleteYN=\"Y\">\n");

    $Authors = explode(',', $a);

    foreach ($Authors as $Author) {
        $Author = trim($Author);

        $i = explode(' ', $Author);

        $Lname = $i[0];

        $Fname = $i[1];

        fwrite($file, "<Author>\n");

        fwrite($file, "<LastName>$Lname</LastName>\n");

        fwrite($file, "<ForeName>$Fname</ForeName>\n");

        fwrite($file, "<Initials>$Fname</Initials>\n");

        fwrite($file, "</Author>\n");
    }

    fwrite($file, "</AuthorList>\n");

    fwrite($file, "<AuthorListJP>$a_jp</AuthorListJP>\n");

    // Language

    if (0 != mb_strlen($t_jp)) {
        fwrite($file, "<Language>jp</Language>\n");
    } else {
        fwrite($file, "<Language>eng</Language>\n");
    }

    fwrite($file, "<PublicationTypeList>\n");

    if ('j' == $jb) {
        fwrite($file, "<PublicationType>Journal Article</PublicationType>\n");
    } else {
        fwrite($file, "<PublicationType>Book</PublicationType>\n");
    }

    fwrite($file, "</PublicationTypeList>\n");

    fwrite($file, '</Article>');

    // Journal

    fwrite($file, "<MedlineJournalInfo>\n");

    fwrite($file, "<MedlineTA>$j</MedlineTA>\n");

    fwrite($file, "</MedlineJournalInfo>\n");

    // Journal JP

    fwrite($file, "<JournalJPInfo>\n");

    fwrite($file, "<JournalJP>$j_JP</JournalJP>\n");

    fwrite($file, "</JournalJPInfo>\n");

    fwrite($file, "</ppdfArticle>\n");

    fwrite($file, "</ppdfArticleSet>\n");

    fclose($file);

    fclose($fileDTD);
}

function RETRIEVE_XML($dir, $file)
{
    $file_name = $dir . '/' . $file;

    if (!file_exists($file_name)) {
        return false;
    }

    $wopmid_xml = '';

    $file = fopen($file_name, 'rb');

    while (!feof($file)) {
        $wopmid_xml .= fgets($file, 1000);
    }

    fclose($file);

    # Analysis xml data

    $pm_data = [];

    $dom = domxml_open_mem($wopmid_xml);

    $ctx = xpath_new_context($dom);

    $xpath_base = '/ppdfArticleSet/ppdfArticle';

    # Journal

    $xpath = $xpath_base . '/MedlineJournalInfo/MedlineTA';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $Journal = $node->get_content();

        $Journal = mb_convert_encoding($Journal, 'euc-jp', 'UTF-8');

        $Journal = special_char_conv($Journal);
    } else {
        $Journal = '- unknown - ';
    }

    $pm_data['j'] = $Journal;

    # Year

    $xpath = $xpath_base . '/Article/Journal/JournalIssue/PubDate/Year';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $Year = $node->get_content();

        $Year = mb_convert_encoding($Year, 'euc-jp', 'UTF-8');
    } else {
        $xpath = $xpath_base . '/Article/Journal/JournalIssue/PubDate/MedlineDate';

        $x_array = $ctx->xpath_eval($xpath);

        if ($node = $x_array->nodeset[0]) {
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

    if ($node = $x_array->nodeset[0]) {
        $Volume = $node->get_content();

        $Volume = mb_convert_encoding($Volume, 'euc-jp', 'UTF-8');
    } else {
        $Volume = '';
    }

    $pm_data['v'] = $Volume;

    # Page

    $xpath = $xpath_base . '/Article/Pagination/MedlinePgn';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $Page = $node->get_content();

        $Page = mb_convert_encoding($Page, 'euc-jp', 'UTF-8');
    } else {
        $Page = '';
    }

    $pm_data['p'] = $Page;

    # Abstract

    $array = $dom->get_elements_by_tagname('AbstractText');

    if ($array[0]) {
        $Abst = $array[0]->get_content();

        $Abst = mb_convert_encoding($Abst, 'euc-jp', 'UTF-8');

        $Abst = special_char_conv($Abst);
    } else {
        $Abst = '';
    }

    $pm_data['ab'] = $Abst;

    # Title

    $array = $dom->get_elements_by_tagname('ArticleTitle');

    if ($array[0]) {
        $Title = $array[0]->get_content();

        $Title = mb_convert_encoding($Title, 'euc-jp', 'UTF-8');

        $Title = special_char_conv($Title);
    } else {
        $Title = '- unknown -';
    }

    $pm_data['t'] = $Title;

    # Authors

    $Authors = '';

    $array = $dom->get_elements_by_tagname('Author');

    if ($array[0]) {
        for ($i = 0, $iMax = count($array); $i < $iMax; $i++) {
            $parent = $array[$i]->parent_node();

            $array2 = $parent->get_elements_by_tagname('LastName');

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

            $Authors = special_char_conv($Authors);
        }

        $Authors = mb_substr($Authors, 0, -2);
    }

    $pm_data['a'] = $Authors;

    # title JP

    $xpath = $xpath_base . '/Article/ArticleTitleJP';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $titleJP = $node->get_content();

        $titleJP = mb_convert_encoding($titleJP, 'euc-jp', 'UTF-8');

        $titleJP = special_char_conv($titleJP);
    } else {
        $titleJP = '';
    }

    $pm_data['t_jp'] = $titleJP;

    # Author JP

    $xpath = $xpath_base . '/Article/AuthorListJP';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $authorJP = $node->get_content();

        $authorJP = mb_convert_encoding($authorJP, 'euc-jp', 'UTF-8');

        $authorJP = special_char_conv($authorJP);
    } else {
        $authorJP = '';
    }

    $pm_data['a_jp'] = $authorJP;

    # Journal JP

    $xpath = $xpath_base . '/JournalJPInfo/JournalJP';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $journalJP = $node->get_content();

        $journalJP = mb_convert_encoding($journalJP, 'euc-jp', 'UTF-8');

        $journalJP = special_char_conv($journalJP);
    } else {
        $journalJP = '';
    }

    $pm_data['j_jp'] = $journalJP;

    # ID

    $xpath = $xpath_base . '/ppdfIndex';

    $x_array = $ctx->xpath_eval($xpath);

    if ($node = $x_array->nodeset[0]) {
        $c_t1 = $node->get_content();

        $c_t1 = mb_convert_encoding($c_t1, 'euc-jp', 'UTF-8');

        $c_t1 = special_char_conv($c_t1);
    } else {
        $journalJP = '';
    }

    $pm_data['c_t1'] = $c_t1;

    return $pm_data;
}

function special_char_conv($str)
{
    require_once __DIR__ . '/class/functions.php';

    $fn = new functions();

    return $fn->special_char_conv($str);
}
