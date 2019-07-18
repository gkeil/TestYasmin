<?php

// Include composer autoloader
require(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/CommandHandler.php');

/*
 * ****************************
 * Actions before the loop starts  
 * 
 */


// Get configuration data
$config = array();
$config = json_decode(file_get_contents(__DIR__.'/config.json'), true);

// Create ReactPHP event loop
$loop = \React\EventLoop\Factory::create();

try {

    // Create the client
    $client = new \CharlotteDunois\Yasmin\Client(array(), $loop);
    
    // Create command handler object
    $CmdHandler = new CommandHandler( $client, $config);
    
    // load commands
    $CmdHandler->RegisterCommands();
    
    // add event handlers
    
    // on Ready
    $client->on('ready', 
                function () use ($client) {
                    echo 'Successfully logged into '.$client->user->tag.PHP_EOL;
    });
        
    
    // Listen for messages
    $client->on("message", 
                 function (\CharlotteDunois\Yasmin\Models\Message $message) use ($CmdHandler) 
                 {
            
                    // handle message from guild (server)
                     $CmdHandler->process_message( $message );
                        
                 }  // end on message
        
    );
    
    // on error
    $client->on('error',
        function (\Throwable $e) use ($client) {
            echo 'Error Found: '.$e->getMessage().PHP_EOL;
        });
    
        
        
    ////////////////////////////////////////////////////////////////////////    
    // login in Discord
     
    $client->login($config["token"])
        ->then (// promise sucessfully fullfiled
                function ($rcv) {
                    echo "SUCCESS\n";
                    var_dump($rcv);
                    }
                );
        
        
    // start event loop
    $loop->run();
    
}
catch ( \Exception $e)
{
    echo $e->message;
}


