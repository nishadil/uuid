<?php

use PHPUnit\Framework\TestCase;

final class UuidV7Test extends TestCase
{
    public function testV7Format()
    {
        $uuid = \Nishadil\Uuid\Uuid::v7()->get();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-7[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            "UUID v7 should match RFC-4122 v7 pattern"
        );
    }

    public function testV7TimestampCloseToNow()
    {
        $uuid = \Nishadil\Uuid\Uuid::v7()->get();
        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts);

        $timeHex = $parts[0] . $parts[1];
        $timeMs = hexdec($timeHex);
        $nowMs = (int) (microtime(true) * 1000);

        $this->assertTrue(
            abs($nowMs - $timeMs) < 10000,
            'v7 timestamp should be within 10 seconds of current time'
        );
    }

    public function testVersionAndVariantBits()
    {
        $uuid = \Nishadil\Uuid\Uuid::v7()->get();

        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts);

        $this->assertMatchesRegularExpression('/^7[0-9a-f]{3}$/i', $parts[2], 'Version nibble must be 7 for v7');
        $this->assertMatchesRegularExpression('/^[89ab][0-9a-f]{3}$/i', $parts[3], 'Variant must be RFC 4122 (8,9,a,b)');
    }
}
