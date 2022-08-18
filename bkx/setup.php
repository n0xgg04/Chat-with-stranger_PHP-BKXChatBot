<?php

$chatbotId=isset($_GET['chatbotId'])?$_GET['chatbotId']:"";
$CBList=json_decode(file_get_contents('./server/list.json'),true);
$accessToken = $CBList[$chatbotId]['accessToken']; 

include_once 'autoload.php';
$bot = new \KaiserStudio5\ChatFramework($accessToken, TRUE);
$builder = new \KaiserStudio5\MessageBuilder();
$bot->setupGettingStarted("#START_BOT");