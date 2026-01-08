<?php

namespace Nishadil\Uuid;

use Nishadil\Uuid\Exception\InvalidArgumentException;

class Factory{

	use UuidTrait;

	public function __construct(){
	}


	public static function generate( array $NISHADIL_UUID_PREPDATA ): ?string {
		if (!array_key_exists('NISHADIL_UUID_VERSION', $NISHADIL_UUID_PREPDATA)) {
			throw new InvalidArgumentException('UUID version must be specified');
		}

		// Read version/node/clockseq from the prepared data.
		$version = (int) $NISHADIL_UUID_PREPDATA['NISHADIL_UUID_VERSION'];

		// Ensure version is in allowed range
		if ($version < 1 || $version > 8) {
			throw new InvalidArgumentException('Invalid UUID version requested: ' . $version);
		}

		$node = $NISHADIL_UUID_PREPDATA['NISHADIL_UUID_NODE'] ?? null;
		$clockseq = $NISHADIL_UUID_PREPDATA['NISHADIL_UUID_CLOCKSEQ'] ?? null;

		switch ($version) {
			case 1:
				return self::v1($version, $node, $clockseq);
			case 2:
				return self::v2($version, $node, $clockseq, $NISHADIL_UUID_PREPDATA);
			case 3:
				return self::v3($version, $node, $clockseq, $NISHADIL_UUID_PREPDATA);
			case 4:
				return self::v4($version, $node, $clockseq);
			case 5:
				return self::v5($version, $node, $clockseq, $NISHADIL_UUID_PREPDATA);
			case 6:
				return self::v6();
			case 7:
				return self::v7();
			case 8:
				return self::v8();
			default:
				return null;
		}
	}

}

?>
