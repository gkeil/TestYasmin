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
        parent::__construct($handler, 'help2', 'Shows list of available commands.');
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
        // Show list of commands as an embed
        $embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed();
        
        $embed
        //->setColor(15641692)                
        ->setColor(0x5CACEE)                // set Color of bar
        //->setTitle("List of Commands")      // Title
        ->addField("List of Commands".PHP_EOL, $out);               // list of commands
        
        // Send the message
        
        // We do not need another promise here, so
        // we call done, because we want to consume the promise
        $message->channel->send('', array('embed' => $embed))
        ->done( null, 
                function ($error) {
                    // We will just echo any errors for this example
                    echo $error.PHP_EOL;
                });
        
        
            
        //$message->channel->send( $out );
    }
}

);