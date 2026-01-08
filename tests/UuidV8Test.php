<?php

use PHPUnit\Framework\TestCase;

final class UuidV8Test extends TestCase
{
    public function testV8RequiresCustomData()
    {
        $this->expectException(\Nishadil\Uuid\Exception\InvalidArgumentException::class);
        \Nishadil\Uuid\Uuid::v8()->get();
    }

    public function testV8CustomHexDeterministic()
    {
        $uuid = \Nishadil\Uuid\Uuid::v8()
            ->withCustomHex('00000000000000000000000000000000')
            ->get();

        $this->assertEquals(
            '00000000-0000-8000-8000-000000000000',
            strtolower($uuid),
            'v8 should preserve custom data while setting version and variant bits'
        );
    }

    public function testV8Format()
    {
        $uuid = \Nishadil\Uuid\Uuid::v8()
            ->withCustomHex('00112233445566778899aabbccddeeff')
            ->get();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-8[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            "UUID v8 should match RFC-4122 v8 pattern"
        );
    }
}
