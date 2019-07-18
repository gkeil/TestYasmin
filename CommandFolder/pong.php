<?php
 // this script implements the pong command
 
require_once('./vendor/autoload.php');
require_once("./CommandAbs.php");
require_once("./CommandHandler.php");

/**
 * Pong command
 */

$this->AddCommand( new class($this) extends Command 
{
    // command Object contructor
    function __construct(CommandHandler $handler) 
    {
        parent::__construct($handler, 'pong', 'Responds with -ping.');
    }
    
    // Command actions
    function run(\CharlotteDunois\Yasmin\Models\Message $message, array $args) : void 
    {
        $message->channel->send('-Ping!');
    }
}

);