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

use const STR_PAD_LEFT;

trait UuidTrait{
    private static $NISHADIL_TIME_INTERVALS_GREGORIAN_TO_UNIX = 0x01b21dd213814000;
    private static $NISHADIL_TIME_INTERVALS_SECOND = 10000000;
    private static $NISHADIL_TIME_INTERVALS_MICROSECOND = 10;
    
    
    public static function v1( $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ ){
        
        $uuidTime = self::getUuidTime();

        $time = str_pad( (string) $uuidTime, 16, '0', STR_PAD_LEFT);

        // convert time hexa to binary
        $time = (string) hex2bin($time);

        // Reorder bytes to their proper locations in the UUID
        $uuid = $time[4] . $time[5] . $time[6] . $time[7] . $time[2] . $time[3] . $time[0] . $time[1];

        $uuid .= pack('n*', $NISHADIL_UUID_CLOCKSEQ);

        // set variant
        $uuid[8] = chr(ord($uuid[8]) & 63 | 128);

        // set version
        $uuid[6] = chr(ord($uuid[6]) & 63 | 16);

        $uuid .= hex2bin($NISHADIL_UUID_NODE);

        $uuid = bin2hex($uuid);

        $uuid = substr($uuid, 0, 8)
        . '-'
        . substr($uuid, 8, 4)
        . '-'
        . substr($uuid, 12, 4)
        . '-'
        . substr($uuid, 16, 4)
        . '-'
        . substr($uuid, 20, 12);


        return $uuid;
    }

    public static function v2(){
        return "V2-under development";
    }
    
    public static function v3(){
        return "V3-under development";
    }
    
    public static function v4(){
        return "V4-under development";
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


}


?>
