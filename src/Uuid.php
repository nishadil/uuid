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
	protected static ?array $NISHADIL_UUID_PREPDATA = null;



	protected static ?int $NISHADIL_UUID_VERSION = null;



	protected static ?string $NISHADIL_UUID_NODE = null;



	protected static ?int $NISHADIL_UUID_CLOCKSEQ = null;



	protected static ?string $NISHADIL_UUID_NAMESPACE = null;



	protected static ?string $NISHADIL_UUID_NAME = null;



	protected static ?int $NISHADIL_UUID_LOCAL_DOMAIN = null;



	protected static ?int $NISHADIL_UUID_LOCAL_ID = null;



	protected static ?string $NISHADIL_UUID_CUSTOM_HEX = null;



	protected static string $NISHADIL_UUID_PATTERN = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';



	protected static ?Factory $NISHADIL_UUID_FACTORY = null;



	function __construct() {
		self::setNode();
		self::setFactory();
		self::$NISHADIL_UUID_PREPDATA = null;
		self::$NISHADIL_UUID_CLOCKSEQ = random_int(0, 0x3fff);
		self::$NISHADIL_UUID_NAMESPACE = null;
		self::$NISHADIL_UUID_NAME = null;
		self::$NISHADIL_UUID_LOCAL_DOMAIN = null;
		self::$NISHADIL_UUID_LOCAL_ID = null;
		self::$NISHADIL_UUID_CUSTOM_HEX = null;
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
	public static function v3(?string $namespace = null, ?string $name = null): self {
		self::setUUIDversion(3);
		$instance = new self;
		if ($namespace !== null) {
			$instance->withNamespace($namespace);
		}
		if ($name !== null) {
			$instance->withName($name);
		}
		return $instance;
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
	public static function v5(?string $namespace = null, ?string $name = null): self {
		self::setUUIDversion(5);
		$instance = new self;
		if ($namespace !== null) {
			$instance->withNamespace($namespace);
		}
		if ($name !== null) {
			$instance->withName($name);
		}
		return $instance;
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
	public static function v8(?string $customHex = null): self {
		self::setUUIDversion(8);
		$instance = new self;
		if ($customHex !== null) {
			$instance->withCustomHex($customHex);
		}
		return $instance;
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

		if( self::$NISHADIL_UUID_NODE === null ):
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
	}




	/*
	|----------------------------------------------------------------
	| getUUIDversion function
	|----------------------------------------------------------------
	|
	| @return int
	|
	*/
	public static function getUUIDversion(): int {

		if( self::$NISHADIL_UUID_VERSION === null ):
			throw new InvalidArgumentException('UUID version is not set');
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
		if ($version < 1 || $version > 8) {
			throw new InvalidArgumentException('UUID version must be between 1 and 8');
		}
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
		if (self::$NISHADIL_UUID_VERSION === null) {
			throw new InvalidArgumentException('UUID version must be set before generation');
		}

		switch (self::$NISHADIL_UUID_VERSION) {
			case 1:
				self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION,'NISHADIL_UUID_NODE'=>self::$NISHADIL_UUID_NODE,'NISHADIL_UUID_CLOCKSEQ'=>self::$NISHADIL_UUID_CLOCKSEQ];
				break;
			case 2:
				self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION,'NISHADIL_UUID_NODE'=>self::$NISHADIL_UUID_NODE,'NISHADIL_UUID_CLOCKSEQ'=>self::$NISHADIL_UUID_CLOCKSEQ];
				if (self::$NISHADIL_UUID_LOCAL_DOMAIN !== null) {
					self::$NISHADIL_UUID_PREPDATA['NISHADIL_UUID_LOCAL_DOMAIN'] = self::$NISHADIL_UUID_LOCAL_DOMAIN;
				}
				if (self::$NISHADIL_UUID_LOCAL_ID !== null) {
					self::$NISHADIL_UUID_PREPDATA['NISHADIL_UUID_LOCAL_ID'] = self::$NISHADIL_UUID_LOCAL_ID;
				}
				break;
			case 3:
			case 5:
				if (empty(self::$NISHADIL_UUID_NAMESPACE)) {
					throw new InvalidArgumentException('NISHADIL_UUID_NAMESPACE is required for v' . self::$NISHADIL_UUID_VERSION);
				}
				if (self::$NISHADIL_UUID_NAME === null) {
					throw new InvalidArgumentException('NISHADIL_UUID_NAME is required for v' . self::$NISHADIL_UUID_VERSION);
				}
				self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION,'NISHADIL_UUID_NAMESPACE'=>self::$NISHADIL_UUID_NAMESPACE,'NISHADIL_UUID_NAME'=>self::$NISHADIL_UUID_NAME];
				break;
			case 4:
				self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION,'NISHADIL_UUID_NODE'=>self::$NISHADIL_UUID_NODE,'NISHADIL_UUID_CLOCKSEQ'=>self::$NISHADIL_UUID_CLOCKSEQ];
				break;
			case 6:
				self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION,'NISHADIL_UUID_NODE'=>self::$NISHADIL_UUID_NODE,'NISHADIL_UUID_CLOCKSEQ'=>self::$NISHADIL_UUID_CLOCKSEQ];
				break;
			case 7:
				self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION];
				break;
			case 8:
				if (empty(self::$NISHADIL_UUID_CUSTOM_HEX)) {
					throw new InvalidArgumentException('NISHADIL_UUID_CUSTOM_HEX is required for v8');
				}
				self::$NISHADIL_UUID_PREPDATA = ['NISHADIL_UUID_VERSION'=>self::$NISHADIL_UUID_VERSION,'NISHADIL_UUID_CUSTOM_HEX'=>self::$NISHADIL_UUID_CUSTOM_HEX];
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
	| @return array
	|
	*/
	public static function getPrepareData(): array {

		if( self::$NISHADIL_UUID_PREPDATA === null ):
			self::setPrepareData();
		endif;

		return self::$NISHADIL_UUID_PREPDATA ?? [];
	}


	public function withNode(string $hexNode): self {
		self::$NISHADIL_UUID_NODE = $hexNode;
		return $this;
	}


	public function withClockSeq(int $clockSeq): self {
		self::$NISHADIL_UUID_CLOCKSEQ = $clockSeq;
		return $this;
	}


	public function withNamespace(string $namespace): self {
		self::$NISHADIL_UUID_NAMESPACE = $namespace;
		return $this;
	}


	public function withName(string $name): self {
		self::$NISHADIL_UUID_NAME = $name;
		return $this;
	}


	public function withLocalDomain($domain): self {
		if ($domain === null) {
			self::$NISHADIL_UUID_LOCAL_DOMAIN = null;
			return $this;
		}

		if (is_string($domain)) {
			$domainLower = strtolower($domain);
			if ($domainLower === 'uid' || $domainLower === 'user') {
				$domain = 0;
			} elseif ($domainLower === 'gid' || $domainLower === 'group') {
				$domain = 1;
			}
		}

		self::$NISHADIL_UUID_LOCAL_DOMAIN = (int) $domain;
		return $this;
	}


	public function withLocalId(int $localId): self {
		self::$NISHADIL_UUID_LOCAL_ID = $localId;
		return $this;
	}


	public function withCustomHex(string $customHex): self {
		$customHex = preg_replace('/[^0-9a-fA-F]/', '', $customHex);
		if (strlen($customHex) !== 32) {
			throw new InvalidArgumentException('custom hex must be 32 hex characters');
		}
		self::$NISHADIL_UUID_CUSTOM_HEX = $customHex;
		return $this;
	}


	public function withCustomBytes(string $customBytes): self {
		if (strlen($customBytes) !== 16) {
			throw new InvalidArgumentException('custom bytes must be 16 bytes');
		}
		self::$NISHADIL_UUID_CUSTOM_HEX = bin2hex($customBytes);
		return $this;
	}


}


?>
