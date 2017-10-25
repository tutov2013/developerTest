<?php
$obXml = simplexml_load_file('https://lenta.ru/rss');
$iLimit = 5;

foreach ($obXml->channel->item as $obItem) {
    if (!$iLimit--) {
        break;
    }

    $str = implode("\n", [
            trim($obItem->title),
            trim($obItem->link),
            trim(($obItem->description))
        ]) . "\n\n";

    $str = strip_tags($str);

    echo mb_convert_encoding($str, 'WINDOWS-1251');
}