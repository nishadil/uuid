<?php

use PHPUnit\Framework\TestCase;

final class UuidV2Test extends TestCase
{
    public function testV2Format()
    {
        // generate using the public fluent API (defaults)
        $uuid = \Nishadil\Uuid\Uuid::v2()->get();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-2[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            "UUID v2 should match RFC-4122 v2 pattern"
        );
    }

    public function testV2DeterministicLocalId()
    {
        // Use Factory::generate() to pass prepared data that v2() will read.
        $localId = 12345;
        $prep = [
            'NISHADIL_UUID_VERSION'    => 2,
            'NISHADIL_UUID_LOCAL_DOMAIN' => 0,   // 0 = UID domain per DCE
            'NISHADIL_UUID_LOCAL_ID'   => $localId,
            // keep node/clockseq omitted to use defaults
        ];

        $uuid = \Nishadil\Uuid\Factory::generate($prep);

        $this->assertIsString($uuid, 'Factory::generate should return a UUID string for v2');

        // time_low (first group) should equal localId encoded as 8 hex chars (big-endian)
        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts, 'UUID must contain 5 parts');

        $timeLowHex = $parts[0];
        $expected = sprintf('%08x', $localId);

        $this->assertEquals(
            strtolower($expected),
            strtolower($timeLowHex),
            "For v2 with local id {$localId}, time_low should equal {$expected}"
        );
    }

    public function testVersionAndVariantBits()
    {
        $uuid = \Nishadil\Uuid\Uuid::v2()->get();

        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts);

        // version is first nibble of 3rd group (time_hi_and_version)
        $timeHiAndVersion = $parts[2];
        $this->assertMatchesRegularExpression('/^2[0-9a-f]{3}$/i', $timeHiAndVersion, 'Version nibble must be 2 for v2');

        // variant is in first nibble of 4th group: should be 8,9,a or b
        $clockSeqHi = $parts[3];
        $this->assertMatchesRegularExpression('/^[89ab][0-9a-f]{3}$/i', $clockSeqHi, 'Variant must be RFC 4122 (8,9,a,b)');
    }
}
