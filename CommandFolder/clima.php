<?php
 // this script implements the pong command
 
require_once('./vendor/autoload.php');
require_once("./CommandAbs.php");
require_once("./CommandHandler.php");

/**
 * clima command
 * 
 * This commands send an http GET request to the SMN API
 * The info of the over 200 wheather stations is received in JSON format
 * The city is requewsted in the 1st arg of clima command. The provided city is
 * seached as substring of the reported cities.
 * Data from smn is received in chunks. Info is presented when the last chunk is received 
 * and the 'end' event is emitted from the Response stream.
 */

$this->AddCommand( new class($this) extends Command 
{
    // Servicio Meteorologico Nacional URL
    private $url = "https://ws.smn.gob.ar/map_items/weather";
    private $message;
    private $args;
    
    // ===========================
    // command Object contructor
    function __construct(CommandHandler $handler) 
    {
        parent::__construct($handler, 'clima', 'Shows wheather conditions at specified city');
    }
    
    // ================
    // Command actions
    function run(\CharlotteDunois\Yasmin\Models\Message $message, array $args) : void 
    {
        // save for later use
        $this->message = $message;
        $this->args = $args;
        
        // check if the city is specified
        if ( empty($args) )
        {
            // no argument supplied
            $message->channel->send( "Must specify city name ( only part is required )\n" );
            return;
        }
        
        // OK. The Go On with the request
        //
        // get the React event loop.
        $loop = $message->client->getLoop();
        
        // 
        try
        {
            // Create HTTP client object
            $smnclient = new React\HttpClient\Client($loop);
            
            // prepare Request
            $request = $smnclient->request('GET', $this->url);
            
            // handle response
            // 'request' implements writablestrema. So handle on response event
            $request->on(   'response',
                
                function (React\HttpClient\Response $response)
                {
                    // This variable will store concatenate the segments of response 
                    // as they are received 
                    static $answer = "";
                    
                    // handle response segments
                    // Response implements readablestream, so handle on data event
                    
                    $response->on('data', function ($chunk) use(&$answer) {
                        // echo $chunk;
                        echo "rcv\n";
                        $answer .= $chunk;
                    }
                    ); // end response on 'data'
                    
                    $response->on('end', function() use(&$answer) {
                            echo "\n**DONE**\n";
                            //echo $answer.PHO_EOL;
                            
                            // Use the received info to create the reply.
                            $this->replyinfo($answer);
                    }
                    ); // end response on 'end'
            }
            );      // end on response
            
            // handle any error that may occur
            $request->on('error', function (\Exception $e) {
                echo $e;
            });
                
            // Actually send the request
            $request->end();
                
                
        // Trap all errora here
        } catch ( Exception $e) {
            echo $e->getMessage();
        }
        
            
    }   // run
    
    //====================================
    // replyinfo()
    // this function creates the response to command
    //
    function replyinfo(string $answer)
    {
        $cities_info = array();  // array holding wheather info of matching cities
        
        // set the local language
        //setlocale(LC_CTYPE, 'es_ES');
        //setlocale(LC_CTYPE, 'en_GB');
        
        // convert Json into array
        $data = json_decode($answer, true);
        
        // search the response for the requested city
        // all cities matching the request are reported
        foreach ( $data as $city )
        {
            // check requested city against city name
            
            // get the strings with non accent and lowercase
            $city_name   = $this->normalize_string( $city['name'] );
            $target_city = $this->normalize_string( $this->args[0]);
            
            // search for the target city in the city name of this array element
            $pos = strpos( $city_name, $target_city );
            
            if ( $pos !== false)
            {
                // the city name matches the request
                $info = new stdClass();
                
                // TODO Add status as image
                // extract city info
                $info->name     = $city['name'];
                $info->temp		= $city['weather']['temp'];
                $info->humi		= $city['weather']['humidity'];
                $info->st		= $city['weather']['st'];
                
                if ( !$info->st )	// validate ST
                    $info->st = $info->temp;
                
                // add to array
                $cities_info[] = $info;
                
            }
            
        }   // end for $data
        
        // check if we have at least 1 matching city
        if ( empty($cities_info) )
        {
            // inform that no city matches the request
            $this->message->channel->send( "No city matches request\n" );
        }
        else
        {
            // create embed with all the info
            $embed = new \CharlotteDunois\Yasmin\Models\MessageEmbed();
            
            // Build the embed
            $embed
            ->setTitle('Climate info')                              // Set a title
            ->setColor(0x5CACEE);                                   // Set a color (the thing on the left side)
            
            // add info of each matching city
            foreach($cities_info as $info )
            {
                    // TODO Handle limit of 25 fields per Embed
                $embed
                    ->addField($info->name, '----')                 // Add city name
                    ->addField('Temp', $info->temp, true)           // Add Temp inline field
                    ->addField('Hum ', $info->humi, true)           // Add Hum inline field
                    ->addField('ST  ', $info->st,   true);          // Add ST inline field
            }
            
            // finish embed
            $embed
            ->setFooter('Data from Servicio Metorologico Nacional');              // Set a footer without icon
            
            
            // Send the message
            
            // We do not need another promise here, so
            // we call done, because we want to consume the promise
            $this->message->channel->send('', array('embed' => $embed))
            ->done(null, function ($error) {
                // We will just echo any errors for this example
                echo $error.PHP_EOL;
            });
            
        }
        
    }   // end replyinfo
    
    //=====================================
    // nomalize_string()
    // converts to lowercase and eliminates accents
    private function normalize_string( string $in ) : string
    {
        $search = array( 'á','é','í','ó','ú','ü');
        $replace = array('a','e','i','o','u','u');
        
        
        return strtolower(str_replace($search, $replace, $in));
    }
    
} // end class

);  // end Add Command