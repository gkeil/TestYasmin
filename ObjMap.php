<?php

/* 
 * This Class implements a map of ojects.
 * key => value 
 * Keys must be of string type
 * Values can be of anytype, but it is intended to be objects
 * keys are converted to lowercase, so the keys are caseinsensitive
 * */


/** 
 * @author Guille
 * 
 */
final class ObjMap
{
    private $map = array();             // create empty array
    
    /**
     */
    public function __construct()
    {
        
    }
    
    // put()
    // add an element to map
    public function put( string $key, $obj ) : void
    {
        // add element to array
        $this->map[ strtolower($key) ] = $obj;
    }
    
    // has()
    // returns true if the key is set
    public function has( string $key ) : bool
    {
        // verify if the key is defined and teh value is not null
        return isset( $this->map[ strtolower($key) ] );
    }
    
    // get()
    // returns the object associated with the key, if set
    // otherwise returns null
    public function get( string $key ) 
    {
        // verify if the key is set
        if ( ! $this->has(strtolower($key)) )
            return null;
        else
            return $this->map[ strtolower($key) ];
        
    }
    
    // len()
    // return the number of items in teh map
    public function len() : int
    {
        return count( $this->map );
    }
    
    // clear()
    // deletes all the entrie sin teh map
    public function clear() : void
    {
        // erase teh present array
        unset( $this->map );
        
        // allocate new one
        $this->map = array();
    }
    
    //list
    // returns array of ojects
    public function getMap() : array
    {             
        return $this->map;   
    }
    
}   // end class

