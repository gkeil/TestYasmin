<?php

// Include composer autoloader
require(__DIR__.'/vendor/autoload.php');
require_once(__DIR__.'/ObjMap.php');
require_once(__DIR__.'/CommandAbs.php');


// 



/** 
 * @author Guille
 * 
 */
final class CommandHandler
{

    public $client;                     // instance of client logged to Discord
    
    private $commands;                   // list of command objects

    private $CommandsPath = __DIR__.'/CommandFolder/*.php';
    
    private $config;                    // bot config array
    /**
     */
    public function __construct( \CharlotteDunois\Yasmin\Client $client, array $config)  
    {
        $this->commands = new ObjMap();       // create map
        
        $this->client = $client;        // save Discord Client
        
        $this->config = $config;        // save config
        
    }
    
    // Process_message
    public function process_message( \CharlotteDunois\Yasmin\Models\Message $message ) 
    {
        // get the Bot prefix for future use
        $prefix = $this->config["prefix"];
        
        // Ignore messages from any Bot (including this, of course)
        if($message->author->bot) {
            return;
        }
        
        // Identify commands based on prefix. Special case for Bots mention (@name)
        
        $mention = $message->guild->me->__toString();
        $mention_pos = strpos( $message->content, $mention );
        
        $prefix_pos  = strpos( $message->content, $prefix );
        
        
        if ( $prefix_pos === false && $mention_pos === false )
        {
            // the message does not include neither the prefix string
            // nor the @username mention
            
            return;
        }
        else if ( $mention_pos === 0 )		// included and at the beggining
        {
            // OK. It is metioning the BOT
            // reply to Mention with indications for info and help
            $message->channel->send( "prefix=$prefix Please type {$prefix}info for Bot info, {$prefix}help for commands list "	);
            
        }
        else if ( $prefix_pos === 0 )		// included and at the beggining
        {
            // OK. The message starts with the prefix: It is a command.
            
            // separate each word in message.                      
            // Explode the content by whitespace
            $args = \explode(' ', $message->content);
            
            // First word is the commnad with prefix
            $firstWord = \array_shift($args);
            
            // we got a command
            $cmdname = strtolower( substr( $firstWord, strlen($prefix) ) );
            
            
            // TEST
            // echo $cmdname."\n";
            
            // check if the command is supported
            if ( $this->commands->has( $cmdname ) )
            {
                // execute the command 
                try 
                {
                    $this->commands->get($cmdname)->run( $message, $args );
                    
                }
                // trap any error occurred in execution
                catch ( Exception $e)
                {
                    echo "Error : ".$e->getMessage."\n";
                }
                
                
            }
        }
        
    }
    
    
    // function to add a new command
    private function AddCommand( Command $cmdObj)
    {
        // add to the $commands map
        $this->commands->put( $cmdObj->getName() , $cmdObj );
    }
    
    // Get all the Commnds in the command directory
    public function RegisterCommands()
    {
        // seach for command files and register add commands to map
              
        foreach( glob( $this->CommandsPath ) as $cmdfile )
        {
            // Add Command by including the file
            include( $cmdfile );
            // echo $cmdfile;   // TODO remove later
        }
    }
    
    // ListCmds
    // returns list with all the command names on screen
    public function listCmds() : array
    {
        $list = array();
        
        // use the map list function
        $map = $this->commands->getMap();
        
        foreach ($map as $cmdname => $cmd)
        {
            $list[$cmdname] =  $cmd->getDescription();
        }
        
        return $list;
    }
     
     
    
    
    
}   // end class

