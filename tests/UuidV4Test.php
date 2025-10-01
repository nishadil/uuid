<?php

use PHPUnit\Framework\TestCase;

final class UuidV4Test extends TestCase
{
    public function testV4Format()
    {
        $uuid = \Nishadil\Uuid\Uuid::v4()->get();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            "UUID v4 should match RFC-4122 v4 pattern"
        );
    }

    public function testV4UniquenessSample()
    {
        $count = 100; // small for CI speed
        $uuids = [];

        for ($i = 0; $i < $count; $i++) {
            $uuids[] = \Nishadil\Uuid\Uuid::v4()->get();
        }

        $unique = array_unique($uuids);
        $this->assertCount($count, $unique, "No collisions expected in {$count} v4 UUID samples");
    }

    public function testVersionAndVariantBits()
    {
        $uuid = \Nishadil\Uuid\Uuid::v4()->get();

        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts);

        // version is first nibble of 3rd group (time_hi_and_version)
        $timeHiAndVersion = $parts[2];
        $this->assertMatchesRegularExpression('/^4[0-9a-f]{3}$/i', $timeHiAndVersion, 'Version nibble must be 4 for v4');

        // variant is in first nibble of 4th group: should be 8,9,a or b
        $clockSeqHi = $parts[3];
        $this->assertMatchesRegularExpression('/^[89ab][0-9a-f]{3}$/i', $clockSeqHi, 'Variant must be RFC 4122 (8,9,a,b)');
    }
}
