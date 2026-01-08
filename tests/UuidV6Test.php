<?php

use PHPUnit\Framework\TestCase;

final class UuidV6Test extends TestCase
{
    public function testV6Format()
    {
        $uuid = \Nishadil\Uuid\Uuid::v6()->get();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-6[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            "UUID v6 should match RFC-4122 v6 pattern"
        );
    }

    public function testVersionAndVariantBits()
    {
        $uuid = \Nishadil\Uuid\Uuid::v6()->get();

        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts);

        $this->assertMatchesRegularExpression('/^6[0-9a-f]{3}$/i', $parts[2], 'Version nibble must be 6 for v6');
        $this->assertMatchesRegularExpression('/^[89ab][0-9a-f]{3}$/i', $parts[3], 'Variant must be RFC 4122 (8,9,a,b)');
    }
}
