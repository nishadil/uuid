<?php

namespace Nishadil\Uuid;

use function extract;

class Factory{

    use UuidTrait;

    public function __construct(){
    }


    // public static function generate( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE ): ?string {
    public static function generate( array $NISHADIL_UUID_PREPDATA ): ?string {
        // return $NISHADIL_UUID_VERSION."-".$NISHADIL_UUID_NODE;
        // return self::$UUIDv1->generate();
        extract($NISHADIL_UUID_PREPDATA);

        switch($NISHADIL_UUID_VERSION){
          case 1:
            return self::v1( $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ );
            break;
          case 2:
            return self::v2();
            break;
          case 3:
            return self::v3();
            break;
          case 4:
            return self::v4();
            break;
          case 5:
            return self::v5();
            break;
          case 6:
            return self::v6();
            break;
          case 7:
            return self::v7();
            break;
          case 8:
            return self::v8();
            break;
          default:
            return null;
            break;
        }

    }

}

?>
