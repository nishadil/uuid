<?php

use PHPUnit\Framework\TestCase;

final class UuidV1Test extends TestCase{
	public function testV1Format()
	{
		// Generate one UUID v1
		$uuid = \Nishadil\Uuid\Uuid::v1()->get();


		$this->assertMatchesRegularExpression(
		'/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
		$uuid,
		"UUID v1 should match RFC-4122 v1 pattern"
		);
	}


	public function testV1UniquenessSample()
	{
		// Smoke test for collisions in a small sample
		$count = 100; // keep small for CI speed
		$uuids = [];

		for ($i = 0; $i < $count; $i++) {
			$uuids[] = \Nishadil\Uuid\Uuid::v1()->get();
		}

		$unique = array_unique($uuids);
		$this->assertCount($count, $unique, "No collisions expected in {$count} v1 UUID samples");
	}


	public function testVersionAndVariantBits()
	{
		$uuid = \Nishadil\Uuid\Uuid::v1()->get();


		// Split into fields
		$parts = explode('-', $uuid);
		$this->assertCount(5, $parts);


		// version is first nibble of 3rd group (time_hi_and_version)
		$timeHiAndVersion = $parts[2];
		$this->assertMatchesRegularExpression('/^1[0-9a-f]{3}$/i', $timeHiAndVersion, 'Version nibble must be 1 for v1');


		// variant is in first nibble of 4th group: should be 8,9,a or b
		$clockSeqHi = $parts[3];
		$this->assertMatchesRegularExpression('/^[89ab][0-9a-f]{3}$/i', $clockSeqHi, 'Variant must be RFC 4122 (8,9,a,b)');
	}
}