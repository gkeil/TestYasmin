<?php

// Include composer autoloader
require(__DIR__.'/vendor/autoload.php');

require_once 'CommandHandler.php';


// This Abstract class is the template for creating specific commend handler objects

abstract class Command {
    
    protected $client;          // instance of the Client loggued into Discord
    
    protected $handler;         // instance of command handler object
        
    public $path;               // Will be assigned by the Command Handler
    
    protected $name = null;
    protected $description = null;
    
    function __construct( CommandHandler $handler, string $name, string $description) {
        $this->client = $handler->client;
        $this->handler = $handler;
        
        $this->name = $name;
        $this->description = $description;
    }
    
    /**
     * Returns the command name.
     * @return string;
     */
    function getName() : string {
        return $this->name;
    }
    
    /**
     * Returns the command description.
     * @return string;
     */
    function getDescription() : string {
        return $this->description;
    }
    
    /**
     * Runs the command.
     * @return void
     * @throws \Throwable|\Exception|\Error
     */
    abstract function run(\CharlotteDunois\Yasmin\Models\Message $message, array $args) : void;
}