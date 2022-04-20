<?php

require '../Bootstrap.php';

use SymfonyCertification\Client;
use Symfony\Component\DomCrawler\Crawler;

$client = new Client();
$crawler = $client->request('GET', '/browser-kit/endpoint.php');

echo_with_eol($crawler->eq(1)->nodeName());
//echo_with_eol($crawler->nextAll());

///** @var Crawler $subCrawler */
//foreach ($crawler->nextAll() as $subCrawler) {
//    echo_with_eol($subCrawler->nodeName());
//}
// result: html

//echo_with_eol($crawler->getNode(0)->attr('lang'));
//// result: en
