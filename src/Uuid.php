<?php

namespace Nishadil\Uuid;

use Nishadil\Uuid\Exception\InvalidArgumentException;

use function sprintf;
use function random_int;
use function hex2bin;


class Uuid{


    /*
    |
    | Class variables using for UUID generation
    |
    */
    protected static array $NISHADIL_UUID_PREPDATA;



    protected static int $NISHADIL_UUID_VERSION;



    protected static string $NISHADIL_UUID_NODE;



    protected static int $NISHADIL_UUID_CLOCKSEQ;



    protected static string $NISHADIL_UUID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';



    protected static ?Factory $NISHADIL_UUID_FACTORY = null;



    function __construct() {
        self::setNode();
        self::setFactory();
        self::$NISHADIL_UUID_PREPDATA = [];
        self::$NISHADIL_UUID_CLOCKSEQ = random_int(0, 0x3fff);
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
    public static function v1(): self {
        self::setUUIDversion(1);
        return new self;
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
    public static function v2(): self {
        self::setUUIDversion(2);
        return new self;
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
    public static function v3(): self {
        self::setUUIDversion(3);
        return new self;
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
    public static function v4(): self {
        self::setUUIDversion(4);
        return new self;
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
    public static function v5(): self {
        self::setUUIDversion(5);
        return new self;
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
    public static function v6(): self {
        self::setUUIDversion(6);
        return new self;
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
    public static function v7(): self {
        self::setUUIDversion(7);
        return new self;
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
    public static function v8(): self {
        self::setUUIDversion(8);
        return new self;
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
    public function get(): ?string {
        // return self::getFactory()->generate( self::getUUIDversion(), self::getNode() );
        return self::getFactory()->generate( self::getPrepareData() );
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
            self::setNode();
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
        // self::NISHADIL_UUID_NODE = hex2bin(self::NISHADIL_UUID_NODE);
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
            self::setUUIDversion();
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
        self::$NISHADIL_UUID_VERSION = $version;
    }




    /*
    |----------------------------------------------------------------
    | getFactory function
    |----------------------------------------------------------------
    |
    | @return instanceOf Factory
    |
    */
    public static function getFactory(): Factory {

        if( !self::$NISHADIL_UUID_FACTORY ):
            self::setFactory();
        endif;

        return self::$NISHADIL_UUID_FACTORY;

    }



    /*
    |----------------------------------------------------------------
    | setFactory function
    |----------------------------------------------------------------
    |
    | @return void
    |
    */
    public static function setFactory(): void {
        self::$NISHADIL_UUID_FACTORY = new Factory;
    }




    /*
    |----------------------------------------------------------------
    | setPrepareData function
    |----------------------------------------------------------------
    |
    | @return void
    |
    */
    public static function setPrepareData(): void {

        switch (self::$NISHADIL_UUID_VERSION) {
          case 1:
            self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION,'NISHADIL_UUID_NODE'=>self::$NISHADIL_UUID_NODE,'NISHADIL_UUID_CLOCKSEQ'=>self::$NISHADIL_UUID_CLOCKSEQ];
            break;
          default:
            self::$NISHADIL_UUID_PREPDATA = [];
            break;
        }

    }


    /*
    |----------------------------------------------------------------
    | getPrepareData function
    |----------------------------------------------------------------
    |
    | @return instanceOf Factory
    |
    */
    public static function getPrepareData(): array {

        if( sizeof(self::$NISHADIL_UUID_PREPDATA) < 1 ):
            self::setPrepareData();
        endif;

        return self::$NISHADIL_UUID_PREPDATA;

    }

}


?>
