<?php
 // this script implements the pong command
 
require_once('./vendor/autoload.php');
require_once("./CommandAbs.php");
require_once("./CommandHandler.php");

/**
 * help command
 */

$this->AddCommand( new class($this) extends Command 
{
    // command Object contructor
    function __construct(CommandHandler $handler) 
    {
        parent::__construct($handler, 'help', 'Shows list of available commands.');
    }
    
    // Command actions
    function run(\CharlotteDunois\Yasmin\Models\Message $message, array $args) : void 
    {
        // get the list of commands
        $list = $this->handler->listCmds();
        
        // init output string
        $out = "";
        
        // display list of commands
        foreach ($list as $name => $desc)
        {
            $out .= "$name : $desc ".PHP_EOL;
        }
            
        $message->channel->send( $out );
    }
}

);