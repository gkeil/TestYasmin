<?php

// Include composer autoloader
require(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/ObjMap.php');
require_once(__DIR__.'/CommandAbs.php');
require_once(__DIR__.'/CommandHandler.php');


$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

// create new Commadn handler object

// Create ReactPHP event loop
$loop = \React\EventLoop\Factory::create();
// Create the client
$client = new \CharlotteDunois\Yasmin\Client(array(), $loop);



$cmh = new CommandHandler( $client, $config );

$cmh->RegisterCommands();

// list commands
echo "\n";

$cmh->listCmds();

