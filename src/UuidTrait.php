<?php

namespace Nishadil\Uuid;

use function base_convert;
use function pack;
use function str_pad;
use function preg_match;
use function microtime;
use function chr;
use function ord;
use function is_null;
use function bin2hex;
use function hex2bin;
use function dechex;
use function hrtime;
use function gettimeofday;
use function random_bytes;

use const STR_PAD_LEFT;

trait UuidTrait{
    private static $NISHADIL_TIME_INTERVALS_GREGORIAN_TO_UNIX = 0x01b21dd213814000;
    private static $NISHADIL_TIME_INTERVALS_SECOND = 10000000;
    private static $NISHADIL_TIME_INTERVALS_MICROSECOND = 10;
    
    
    public static function v1( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ ){
        
        $uuidTime = self::getUuidTime();

        $time = str_pad( (string) $uuidTime, 16, '0', STR_PAD_LEFT);

        // convert time hexa to binary
        $v1bytes = (string) hex2bin($time);

        return self::bytesToVersionAndVariant($v1bytes, $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ);
    }

    public static function v2(){
        return "V2-under development";
    }
    
    public static function v3(){
        return "V3-under development";
    }
    
    public static function v4( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ ){
        $v4bytes = bin2hex(random_bytes(16));
        // $v4bytes = str_pad( (string) $v4bytes, 16, '0', STR_PAD_LEFT);
        $v4bytes = (string) hex2bin($v4bytes);

        return self::bytesToVersionAndVariant($v4bytes, $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ);
    }
    
    public static function v5(){
        return "V5-under development";
    }
    
    public static function v6(){
        return "V6-under development";
    }
    
    public static function v7(){
        return "V7-under development";
    }
    
    public static function v8(){
        return "V8-under development";
    }
    
    
    
    
    private static function getUuidTime(){
        $timeOfDay = gettimeofday();

        $seconds = $timeOfDay['sec'];
        $microSeconds = $timeOfDay['usec'];

        $uuidTime = ((int) $seconds * self::$NISHADIL_TIME_INTERVALS_SECOND)
            + ((int) $microSeconds * self::$NISHADIL_TIME_INTERVALS_MICROSECOND)
            + self::$NISHADIL_TIME_INTERVALS_GREGORIAN_TO_UNIX;

        return str_pad(dechex($uuidTime), 16, '0', STR_PAD_LEFT);
    }


    private static function bytesToVersionAndVariant( string $bytes, $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ ):string{

        // Reorder bytes to their proper locations in the UUID
        $uuid = $bytes[4] . $bytes[5] . $bytes[6] . $bytes[7] . $bytes[2] . $bytes[3] . $bytes[0] . $bytes[1];

        $uuid .= pack('n*', $NISHADIL_UUID_CLOCKSEQ);

         // set variant
         $uuid[8] = chr(ord($uuid[8]) & 63 | 128);

         // set version
         $uuid[6] = chr(ord($uuid[6]) & 63 | 16);
 
         $uuid .= hex2bin($NISHADIL_UUID_NODE);
 
         $uuid = bin2hex($uuid);
 
         $uuid = self::uuidOutput( $NISHADIL_UUID_VERSION, (string) $uuid );

        //  $uuid = substr($uuid, 0, 8)
        //  . '-'
        //  . substr($uuid, 8, 4)
        //  . '-'
        //  . substr($uuid, 12, 4)
        //  . '-'
        //  . substr($uuid, 16, 4)
        //  . '-'
        //  . substr($uuid, 20, 12);

    return $uuid;
    }


    private static function uuidOutput(int $NISHADIL_UUID_VERSION, string $uuid): string{
        $uuid = str_split($uuid, 4);
        return sprintf("%08s-%04s-{$NISHADIL_UUID_VERSION}%03s-%04x-%012s",
            $uuid[0] . $uuid[1], $uuid[2],
            substr($uuid[3], 1, 3),
            hexdec($uuid[4]) & 0x3fff | 0x8000,
            $uuid[5] . $uuid[6] . $uuid[7]
        );
    }


}


?>
