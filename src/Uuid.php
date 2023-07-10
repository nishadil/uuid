<?php

namespace Nishadil\Uuid;

use Nishadil\Uuid\Exception\InvalidArgumentException;

use function sprintf;
use function random_int;


class Uuid{


    /*
    |
    | Class variables using for UUID generation
    |
    */
    public int $NISHADIL_UUID_VERSION;

    
    
    public string $NISHADIL_UUID_NODE;



    public string $NISHADIL_UUID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    




    function __construct() {
        if( !self::$NISHADIL_UUID_NODE ):
            self::$NISHADIL_UUID_NODE = self::setNode();
        endif;
    }




    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 1
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using a timestamp and monotonic counter.
    |
    | @return string
    | @throws Exception
    */
    public function v1(): string {
        self::setUUIDversion(1);
    }
    
    
    
    
    
    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 2
    |----------------------------------------------------------------
    |
    | UUID Standard : 
    |
    | @return string
    | @throws Exception
    */
    public function v2(): string {
        self::setUUIDversion(2);
    }
    
    
    
    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 3
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs based on the MD5 hash of some data.
    |
    | @return string
    | @throws Exception
    */
    public function v3(): string {
        self::setUUIDversion(3);
    }
    


    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 4
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs with random data.
    |
    | @return string
    | @throws Exception
    */
    public function v4(): string {
        self::setUUIDversion(4);
    }



    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 5
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs based on the SHA1 hash of some data.
    |
    | @return string
    | @throws Exception
    */
    public function v5(): string {
        self::setUUIDversion(5);
    }




    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 6
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using a timestamp and monotonic counter.
    |
    | @return string
    | @throws Exception
    */
    public function v6(): string {
        self::setUUIDversion(6);
    }



    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 7
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using a Unix timestamp.
    |
    | @return string
    | @throws Exception
    */
    public function v7(): string {
        self::setUUIDversion(7);
    }




    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 8
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using user-defined data.
    |
    | @return string
    | @throws Exception
    */
    public function v8(): string {
        self::setUUIDversion(8);
    }










    /*
    |----------------------------------------------------------------
    | get function to generate and return UUID
    |----------------------------------------------------------------
    |
    | @return string
    | @throws Exception
    |
    */
    public function get(): string {
        return self::getUUIDversion();
    }













    /*
    |----------------------------------------------------------------
    | getNode function
    |----------------------------------------------------------------
    |
    | @return string
    |
    */
    public static function getNode(): string {
        
        if( !self::$NISHADIL_UUID_NODE ):
            self::$NISHADIL_UUID_NODE = self::setNode();
        endif;
        
        return self::$NISHADIL_UUID_NODE;

    }



    /*
    |----------------------------------------------------------------
    | setNode function
    |----------------------------------------------------------------
    |
    | @return void
    |
    */
    public static function setNode(): void {
        self::$NISHADIL_UUID_NODE = sprintf('%06x%06x',
            random_int(0, 0xffffff) | 0x010000,
            random_int(0, 0xffffff)
        );
    }




    /*
    |----------------------------------------------------------------
    | getUUIDversion function
    |----------------------------------------------------------------
    |
    | @return string
    |
    */
    public static function getUUIDversion(): string {
        
        if( !self::$NISHADIL_UUID_VERSION ):
            self::$NISHADIL_UUID_VERSION = self::setUUIDversion();
        endif;
        
        return self::$NISHADIL_UUID_VERSION;

    }



    /*
    |----------------------------------------------------------------
    | setUUIDversion function
    |----------------------------------------------------------------
    |
    | @return void
    |
    */
    public static function setUUIDversion(int $version = 1): void {
        self::$NISHADIL_UUID_NODE = $version;
    }

}


?>