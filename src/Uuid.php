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

    }




    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 1
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using a timestamp and monotonic counter.
    |
    | @params string|null #node
    | @return string
    | @throws Exception
    */
    public function v1( ?string $node = null ): string {

    }





    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 2
    |----------------------------------------------------------------
    |
    | UUID Standard : 
    |
    */
    public function v2() {
        
    }



    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 3
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs based on the MD5 hash of some data.
    |
    */
    public function v3() {
        
    }



    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 4
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs with random data.
    |
    */
    public function v4() {
        
    }



    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 5
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs based on the SHA1 hash of some data.
    |
    */
    public function v5() {
        
    }




    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 6
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using a timestamp and monotonic counter.
    |
    */
    public function v6() {
        
    }



    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 7
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using a Unix timestamp.
    |
    */
    public function v7() {
        
    }




    /*
    |----------------------------------------------------------------
    | UUID generation using UUID standard version 8
    |----------------------------------------------------------------
    |
    | UUID Standard : UUIDs using user-defined data.
    |
    */
    public function v8() {
        
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

}


?>