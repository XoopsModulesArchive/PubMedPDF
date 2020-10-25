<?php

function wopmid($year, $author, $journal, $volume, $pages, $title)
{
    $title = mb_substr(trim($title), 0, 10);

    if ('0' == $year) {
        $year = '0000';
    }

    $wopmid_id = $year . '_' . $author . '_' . $journal . '_' . $volume . '_' . $pages . '_' . $title;

    // echo "before: $wopmid_id"."<br>";

    $wopmid_id = htmlspecialchars($wopmid_id, ENT_NOQUOTES);

    $wopmid_id = str_replace(
        [
            ' ',
            ',',
            '&',
            '\'',
            ':',
            ';',
            '%',
            '?',
            '!',
            '#',
            '\\',
            '/',
            '"',
            '*',
            '|',
            '>',
            '<',
        ],
        [
            '_',
            '~',
            'A',
            'D',
            'C',
            'S',
            'P',
            'Q',
            'X',
            'S',
            'I',
            'L',
            'B',
            'M',
            'O',
            'G',
            'L',
        ],
        $wopmid_id
    );

    // echo "after: $wopmid_id"."<br>";

    return $wopmid_id;
}
