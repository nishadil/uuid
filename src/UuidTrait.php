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
use function random_int;
use function substr;

use const STR_PAD_LEFT;

trait UuidTrait{
    /**
     * Number of 100-ns intervals between
     * Gregorian epoch (1582-10-15) and Unix epoch (1970-01-01)
     */
    private static $NISHADIL_TIME_INTERVALS_GREGORIAN_TO_UNIX = 0x01b21dd213814000;
    private static $NISHADIL_TIME_INTERVALS_SECOND = 10000000;
    private static $NISHADIL_TIME_INTERVALS_MICROSECOND = 10;


    /**
     * Version 1 (time-based) UUID.
     *
     * @param int $NISHADIL_UUID_VERSION
     * @param string|null $NISHADIL_UUID_NODE 12-hex characters (48-bit) or null to use generated node
     * @param int|null $NISHADIL_UUID_CLOCKSEQ 0..0x3fff or null to use random
     * @return string
     */
    public static function v1( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ ){

        $uuidTime = self::getUuidTime();

        // uuidTime is hex string representing the 60-bit (or 64-bit) timestamp
        $time = str_pad( (string) $uuidTime, 16, '0', STR_PAD_LEFT);

        // convert time hexa to binary (8 bytes)
        $v1bytes = (string) hex2bin($time);

        return self::bytesToVersionAndVariant($v1bytes, $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ);
    }

    /**
     * Version 2: DCE Security (POSIX UID/GID embedding)
     *
     * This implementation follows the DCE spec:
     * - time_low (32 bits) replaced with local identifier (POSIX UID/GID or provided integer)
     * - clock_seq_low (lowest 8 bits of clock sequence) replaced with local domain (0=UID,1=GID)
     * - version set to 2, variant set to RFC 4122
     *
     * @param int $NISHADIL_UUID_VERSION
     * @param string|null $NISHADIL_UUID_NODE
     * @param int|array|null $NISHADIL_UUID_CLOCKSEQ $clockseq or array with keys 'domain' and 'local_id' or the prepared data array
     * @param array|null $prep optional prepared data (Factory passes this through)
     * @return string
     */
    public static function v2( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE = null, $NISHADIL_UUID_CLOCKSEQ = null, $prep = null ){
        // get timestamp like v1
        $uuidTime = self::getUuidTime();
        $time = str_pad((string)$uuidTime, 16, '0', STR_PAD_LEFT);
        $timeBin = (string) hex2bin($time); // 8 bytes

        // Build initial uuid binary layout as v1 does: reorder timestamp bytes
        // bytes[4..7] -> time_low (4), bytes[2..3] -> time_mid (2), bytes[0..1] -> time_hi (2)
        if (strlen($timeBin) !== 8) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('unexpected timestamp length for v2');
        }
        $uuid = $timeBin[4] . $timeBin[5] . $timeBin[6] . $timeBin[7] . $timeBin[2] . $timeBin[3] . $timeBin[0] . $timeBin[1];

        // Determine local domain and local id.
        $domain = null;
        $localId = null;
        $clockseq = $NISHADIL_UUID_CLOCKSEQ;

        // 1) If $prep provided and contains keys, prefer them
        if (is_array($prep)) {
            if (isset($prep['NISHADIL_UUID_LOCAL_DOMAIN'])) {
                $domain = (int)$prep['NISHADIL_UUID_LOCAL_DOMAIN'];
            }
            if (isset($prep['NISHADIL_UUID_LOCAL_ID'])) {
                $localId = (int)$prep['NISHADIL_UUID_LOCAL_ID'];
            }
        }

        // 2) Also allow passing domain/localId via $NISHADIL_UUID_CLOCKSEQ as array
        if (is_array($NISHADIL_UUID_CLOCKSEQ)) {
            if (isset($NISHADIL_UUID_CLOCKSEQ['domain'])) {
                $domain = (int)$NISHADIL_UUID_CLOCKSEQ['domain'];
            }
            if (isset($NISHADIL_UUID_CLOCKSEQ['local_id'])) {
                $localId = (int)$NISHADIL_UUID_CLOCKSEQ['local_id'];
            }
            $clockseq = null;
        }

        // Normalize domain: accept some textual shortcuts
        if (is_string($domain)) {
            $d = strtolower($domain);
            if ($d === 'uid' || $d === 'user') {
                $domain = 0;
            } elseif ($d === 'gid' || $d === 'group') {
                $domain = 1;
            } else {
                $domain = (int)$domain;
            }
        }

        // Default domain -> 0 (UID)
        if ($domain === null) {
            $domain = 0;
        }
        $domain = (int) $domain;
        if ($domain < 0 || $domain > 0xff) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('local domain must be between 0 and 255');
        }

        // Determine localId if not provided
        if ($localId === null) {
            // prefer posix functions if available
            if (function_exists('posix_getuid') && $domain === 0) {
                $localId = posix_getuid();
            } elseif (function_exists('posix_getgid') && $domain === 1) {
                $localId = posix_getgid();
            } else {
                // Fallback: try common env fallback or random
                if ($domain === 0 && getenv('UID') !== false) {
                    $localId = (int)getenv('UID');
                } else {
                    // Last resort: random 32-bit number (keeps uniqueness but loses meaningful embedding)
                    $localId = random_int(0, 0xffffffff);
                }
            }
        }

        // Replace time_low (first 4 bytes) with localId (network big-endian)
        $timeLowBin = pack('N', $localId & 0xffffffff);
        // Replace first 4 bytes of $uuid
        $uuid = $timeLowBin . substr($uuid, 4); // keep bytes 4..end

        // Handle clock sequence: generate if not provided
        if ($clockseq === null) {
            $clockseq = random_int(0, 0x3fff);
        } else {
            $clockseq = (int)$clockseq;
            if ($clockseq < 0 || $clockseq > 0x3fff) {
                throw new \Nishadil\Uuid\Exception\InvalidArgumentException('clockseq must be between 0 and 0x3fff');
            }
        }

        // Replace the least significant 8 bits of the clock sequence with domain per DCE spec
        $clockseqWithDomain = (($clockseq & 0xff00) | ($domain & 0xff));

        // append clockseq as network-order 2 bytes
        $uuid .= pack('n', $clockseqWithDomain);

        // Set RFC 4122 variant on clock_seq_hi_and_reserved (byte index 8)
        if (strlen($uuid) < 9) {
            $uuid = str_pad($uuid, 9, "\0", STR_PAD_RIGHT);
        }
        $uuid[8] = chr((ord($uuid[8]) & 0x3f) | 0x80); // set two most significant bits to 10

        // Set version nibble in time_hi_and_version (byte index 6)
        $version = (int)$NISHADIL_UUID_VERSION & 0x0f;
        $uuid[6] = chr((ord($uuid[6]) & 0x0f) | ($version << 4));

        // Node handling (48-bit) — same as v1
        if (empty($NISHADIL_UUID_NODE)) {
            $nodeHex = Uuid::getNode();
        } else {
            $nodeHex = preg_replace('/[^0-9a-fA-F]/', '', (string)$NISHADIL_UUID_NODE);
            if (strlen($nodeHex) !== 12) {
                throw new \Nishadil\Uuid\Exception\InvalidArgumentException('node must be 12 hex characters (48-bit)');
            }
        }
        $uuid .= hex2bin($nodeHex);

        // Convert to hex and format
        $uuidHex = bin2hex($uuid);

        return self::uuidOutput($NISHADIL_UUID_VERSION, (string)$uuidHex);
    }

    public static function v3( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE = null, $NISHADIL_UUID_CLOCKSEQ = null, $prep = null ){
		// Validate prepared data array
		if (!is_array($prep)) {
			throw new \Nishadil\Uuid\Exception\InvalidArgumentException('v3 requires prepared data with NISHADIL_UUID_NAMESPACE and NISHADIL_UUID_NAME');
		}

		if (empty($prep['NISHADIL_UUID_NAMESPACE'])) {
			throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_NAMESPACE is required for v3');
		}
		if (!isset($prep['NISHADIL_UUID_NAME'])) {
			throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_NAME is required for v3');
		}

		$ns = (string)$prep['NISHADIL_UUID_NAMESPACE'];
		$name = (string)$prep['NISHADIL_UUID_NAME'];

		// Normalize namespace into 16-byte binary
		// Accept canonical form with hyphens or 32-hex char string.
		$nsHex = preg_replace('/[^0-9a-fA-F]/', '', $ns);
		if (strlen($nsHex) !== 32) {
			throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_NAMESPACE must be a valid UUID (36-char canonical or 32-char hex)');
		}
		$nsBin = hex2bin($nsHex);
		if ($nsBin === false) {
			throw new \Nishadil\Uuid\Exception\InvalidArgumentException('Invalid namespace hex for v3');
		}

		// Compute MD5 hash of namespace (binary) + name (raw bytes)
		$raw = md5($nsBin . $name, true); // 16 bytes

		// Set version (top 4 bits of byte index 6)
		$raw[6] = chr((ord($raw[6]) & 0x0f) | ( ( (int)$NISHADIL_UUID_VERSION & 0x0f ) << 4 ));

		// Set variant to RFC 4122 (10xxxxxx) in byte index 8
		$raw[8] = chr((ord($raw[8]) & 0x3f) | 0x80);

		// Convert to hex and format using existing helper
		$hex = bin2hex($raw);

		return self::uuidOutput((int)$NISHADIL_UUID_VERSION, (string)$hex);
	}

    public static function v4( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ ){
        $randomBytes = random_bytes(16);
        $hex = bin2hex($randomBytes);

        return self::uuidOutput((int)$NISHADIL_UUID_VERSION, (string)$hex);
    }

    public static function v5( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE = null, $NISHADIL_UUID_CLOCKSEQ = null, $prep = null ){
        if (!is_array($prep)) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('v5 requires prepared data with NISHADIL_UUID_NAMESPACE and NISHADIL_UUID_NAME');
        }

        if (empty($prep['NISHADIL_UUID_NAMESPACE'])) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_NAMESPACE is required for v5');
        }
        if (!isset($prep['NISHADIL_UUID_NAME'])) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_NAME is required for v5');
        }

        $ns = (string)$prep['NISHADIL_UUID_NAMESPACE'];
        $name = (string)$prep['NISHADIL_UUID_NAME'];

        $nsHex = preg_replace('/[^0-9a-fA-F]/', '', $ns);
        if (strlen($nsHex) !== 32) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_NAMESPACE must be a valid UUID (36-char canonical or 32-char hex)');
        }
        $nsBin = hex2bin($nsHex);
        if ($nsBin === false) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('Invalid namespace hex for v5');
        }

        $raw = sha1($nsBin . $name, true); // 20 bytes
        $raw = substr($raw, 0, 16);

        $raw[6] = chr((ord($raw[6]) & 0x0f) | ( ( (int)$NISHADIL_UUID_VERSION & 0x0f ) << 4 ));
        $raw[8] = chr((ord($raw[8]) & 0x3f) | 0x80);

        $hex = bin2hex($raw);

        return self::uuidOutput((int)$NISHADIL_UUID_VERSION, (string)$hex);
    }

    public static function v6( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE = null, $NISHADIL_UUID_CLOCKSEQ = null, $prep = null ){
        $timeHex = self::getUuidTime();
        if (strlen($timeHex) !== 16) {
            $timeHex = str_pad($timeHex, 16, '0', STR_PAD_LEFT);
        }

        $timeHex60 = substr($timeHex, 1); // drop the top nibble to get 60-bit timestamp
        $timeHigh = substr($timeHex60, 0, 8);
        $timeMid = substr($timeHex60, 8, 4);
        $timeLow = substr($timeHex60, 12, 3);
        $timeHiAndVersion = '0' . $timeLow;

        if (is_null($NISHADIL_UUID_CLOCKSEQ)) {
            $NISHADIL_UUID_CLOCKSEQ = random_int(0, 0x3fff);
        } else {
            $NISHADIL_UUID_CLOCKSEQ = (int) $NISHADIL_UUID_CLOCKSEQ;
            if ($NISHADIL_UUID_CLOCKSEQ < 0 || $NISHADIL_UUID_CLOCKSEQ > 0x3fff) {
                throw new \Nishadil\Uuid\Exception\InvalidArgumentException('clockseq must be between 0 and 0x3fff');
            }
        }
        $clockSeqHex = sprintf('%04x', $NISHADIL_UUID_CLOCKSEQ);

        if (empty($NISHADIL_UUID_NODE)) {
            $nodeHex = Uuid::getNode();
        } else {
            $nodeHex = preg_replace('/[^0-9a-fA-F]/', '', (string)$NISHADIL_UUID_NODE);
            if (strlen($nodeHex) !== 12) {
                throw new \Nishadil\Uuid\Exception\InvalidArgumentException('node must be 12 hex characters (48-bit)');
            }
        }

        $hex = $timeHigh . $timeMid . $timeHiAndVersion . $clockSeqHex . $nodeHex;

        return self::uuidOutput((int)$NISHADIL_UUID_VERSION, (string)$hex);
    }

    public static function v7( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE = null, $NISHADIL_UUID_CLOCKSEQ = null, $prep = null ){
        $timeMs = (int) (microtime(true) * 1000);
        if ($timeMs < 0 || $timeMs > 0xffffffffffff) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('timestamp out of range for v7');
        }

        $timeHex = sprintf('%012x', $timeMs);
        $timeLow = substr($timeHex, 0, 8);
        $timeMid = substr($timeHex, 8, 4);

        $randA = random_int(0, 0xfff);
        $randAHex = sprintf('%03x', $randA);
        $timeHiAndVersion = '0' . $randAHex;

        $randB = random_bytes(8);
        $randB[0] = chr(ord($randB[0]) & 0x3f); // clear top 2 bits; uuidOutput sets RFC variant
        $randBHex = bin2hex($randB);

        $clockSeqHex = substr($randBHex, 0, 4);
        $nodeHex = substr($randBHex, 4, 12);

        $hex = $timeLow . $timeMid . $timeHiAndVersion . $clockSeqHex . $nodeHex;

        return self::uuidOutput((int)$NISHADIL_UUID_VERSION, (string)$hex);
    }

    public static function v8( $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE = null, $NISHADIL_UUID_CLOCKSEQ = null, $prep = null ){
        if (!is_array($prep)) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('v8 requires custom data');
        }

        $customHex = $prep['NISHADIL_UUID_CUSTOM_HEX'] ?? null;
        if ($customHex === null && isset($prep['NISHADIL_UUID_CUSTOM_BYTES'])) {
            $customBytes = $prep['NISHADIL_UUID_CUSTOM_BYTES'];
            if (!is_string($customBytes) || strlen($customBytes) !== 16) {
                throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_CUSTOM_BYTES must be 16 bytes');
            }
            $customHex = bin2hex($customBytes);
        }

        if ($customHex === null) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_CUSTOM_HEX is required for v8');
        }

        $customHex = preg_replace('/[^0-9a-fA-F]/', '', (string)$customHex);
        if (strlen($customHex) !== 32) {
            throw new \Nishadil\Uuid\Exception\InvalidArgumentException('NISHADIL_UUID_CUSTOM_HEX must be 32 hex characters');
        }

        return self::uuidOutput((int)$NISHADIL_UUID_VERSION, (string)$customHex);
    }



    /**
     * Return UUID timestamp as hex string (padded to 16 hex chars).
     * This is the number of 100-ns intervals since Gregorian epoch.
     *
     * @return string
     */
    private static function getUuidTime(){
        $timeOfDay = gettimeofday();

        $seconds = $timeOfDay['sec'];
        $microSeconds = $timeOfDay['usec'];

        $uuidTime = ((int) $seconds * self::$NISHADIL_TIME_INTERVALS_SECOND)
            + ((int) $microSeconds * self::$NISHADIL_TIME_INTERVALS_MICROSECOND)
            + self::$NISHADIL_TIME_INTERVALS_GREGORIAN_TO_UNIX;

        return str_pad(dechex($uuidTime), 16, '0', STR_PAD_LEFT);
    }


    /**
     * Build the final UUID binary, set version & variant bits and format.
     *
     * Important:
     * - $bytes: binary string (expected 8 bytes for timestamp-based layout or 16 for random)
     * - $NISHADIL_UUID_NODE: 12 hex characters (48-bit) OR null
     * - $NISHADIL_UUID_CLOCKSEQ: integer (0..0x3fff) OR null
     *
     * @return string canonical UUID string
     */
    private static function bytesToVersionAndVariant( string $bytes, $NISHADIL_UUID_VERSION, $NISHADIL_UUID_NODE, $NISHADIL_UUID_CLOCKSEQ ):string{

        // When called from v1: $bytes is 8-byte timestamp (big-endian). Reorder to
        // time_low (4 bytes), time_mid (2 bytes), time_hi_and_version (2 bytes).
        // For v4: $bytes may be 16 random bytes already - keep compatible path.
        if (strlen($bytes) === 8) {
            // Reorder bytes to their proper locations in the UUID:
            // bytes[4..7] -> time_low (4), bytes[2..3] -> time_mid (2), bytes[0..1] -> time_hi (2)
            $uuid = $bytes[4] . $bytes[5] . $bytes[6] . $bytes[7] . $bytes[2] . $bytes[3] . $bytes[0] . $bytes[1];
            // append clockseq (2 bytes, network order)
        } else {
            // If we got 16 bytes (random), use first 8 as time-related and remaining as rest
            // Keep it simple and take first 8 bytes as-is — calling code for v4 will still set version correctly below.
            $uuid = substr($bytes, 0, 8);
            $uuid .= substr($bytes, 8, 2); // clockseq placeholder area (will be overwritten)
        }

        // handle clock sequence: must be 14-bit value (0..0x3fff)
        if (is_null($NISHADIL_UUID_CLOCKSEQ)) {
            // generate random 14-bit
            $NISHADIL_UUID_CLOCKSEQ = random_int(0, 0x3fff);
        } else {
            $NISHADIL_UUID_CLOCKSEQ = (int) $NISHADIL_UUID_CLOCKSEQ;
            if ($NISHADIL_UUID_CLOCKSEQ < 0 || $NISHADIL_UUID_CLOCKSEQ > 0x3fff) {
                throw new \Nishadil\Uuid\Exception\InvalidArgumentException('clockseq must be between 0 and 0x3fff');
            }
        }

        $uuid .= pack('n', $NISHADIL_UUID_CLOCKSEQ); // 2 bytes

        // Ensure we have room for variant byte; if not, pad
        if (strlen($uuid) < 10) {
            $uuid = str_pad($uuid, 10, "\0", STR_PAD_RIGHT);
        }

        // Set RFC 4122 variant: the two most significant bits of the clock_seq_hi_and_reserved to 10
        // clock_seq_hi_and_reserved is the first byte of the 2-byte clockseq we appended
        $uuid[8] = chr((ord($uuid[8]) & 0x3f) | 0x80); // 0x3f = 0011 1111, 0x80 = 1000 0000

        // Set version: top 4 bits of the time_hi_and_version field (which is at bytes index 6 and 7)
        // Preserve the low 4 bits of that octet and set high nibble to version.
        $version = (int) $NISHADIL_UUID_VERSION & 0x0f;
        $uuid[6] = chr((ord($uuid[6]) & 0x0f) | ($version << 4));

        // Node: must be 48-bit (12 hex chars). If not provided, use generated node from Uuid class.
        if (empty($NISHADIL_UUID_NODE)) {
            // call Uuid::getNode() from same namespace/class
            $nodeHex = Uuid::getNode();
        } else {
            // Normalize input: accept hex string possibly with separators; strip non-hex
            $nodeHex = preg_replace('/[^0-9a-fA-F]/', '', (string)$NISHADIL_UUID_NODE);
            if (strlen($nodeHex) !== 12) {
                throw new \Nishadil\Uuid\Exception\InvalidArgumentException('node must be 12 hex characters (48-bit)');
            }
        }

        // append node as binary
        $uuid .= hex2bin($nodeHex);

        // Turn binary to hex string for formatting
        $uuid = bin2hex($uuid);

        // Format into canonical UUID string (uses existing uuidOutput helper)
        $uuid = self::uuidOutput( $NISHADIL_UUID_VERSION, (string) $uuid );

        return $uuid;
    }


    /**
     * Format a 32-hex string (binary->hex) into canonical UUID text.
     * This helper expects $uuid to be the hex representation of the 16 bytes.
     *
     * @param int $NISHADIL_UUID_VERSION
     * @param string $uuid 32* hex string
     * @return string formatted UUID
     */
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
