<?php

use PHPUnit\Framework\TestCase;

final class UuidV3Test extends TestCase
{
    public function testV3Format()
    {
        $prep = [
            'NISHADIL_UUID_VERSION'   => 3,
            'NISHADIL_UUID_NAMESPACE' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8', // DNS namespace
            'NISHADIL_UUID_NAME'      => 'www.example.com',
        ];

        // Use Factory::generate so prepared-data is passed correctly
        $uuid = \Nishadil\Uuid\Factory::generate($prep);

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-3[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            "UUID v3 should match RFC-4122 v3 pattern"
        );
    }

    public function testV3Deterministic()
    {
        $ns = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; // DNS namespace (RFC)
        $name = 'www.example.com';

        // Build expected UUID using the same algorithm (MD5(namespace_bin + name))
        $nsHex = preg_replace('/[^0-9a-fA-F]/', '', $ns);
        $this->assertEquals(32, strlen($nsHex), 'Namespace must normalize to 32 hex chars for test');

        $nsBin = hex2bin($nsHex);
        $this->assertNotFalse($nsBin, 'Namespace hex must be valid');

        $raw = md5($nsBin . $name, true); // 16 bytes

        // set version = 3
        $raw[6] = chr((ord($raw[6]) & 0x0f) | (3 << 4));
        // set RFC 4122 variant (10xx)
        $raw[8] = chr((ord($raw[8]) & 0x3f) | 0x80);

        $hex = bin2hex($raw);
        $expected = sprintf('%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );

        // Generate using library
        $prep = [
            'NISHADIL_UUID_VERSION'   => 3,
            'NISHADIL_UUID_NAMESPACE' => $ns,
            'NISHADIL_UUID_NAME'      => $name,
        ];
        $actual = \Nishadil\Uuid\Factory::generate($prep);

        $this->assertEquals(strtolower($expected), strtolower($actual), 'v3 UUID must be deterministic and match MD5-derived value');
    }

    public function testVersionAndVariantBits()
    {
        $prep = [
            'NISHADIL_UUID_VERSION'   => 3,
            'NISHADIL_UUID_NAMESPACE' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
            'NISHADIL_UUID_NAME'      => 'www.example.com',
        ];

        $uuid = \Nishadil\Uuid\Factory::generate($prep);
        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts);

        $this->assertMatchesRegularExpression('/^3[0-9a-f]{3}$/i', $parts[2], 'Version nibble must be 3 for v3');
        $this->assertMatchesRegularExpression('/^[89ab][0-9a-f]{3}$/i', $parts[3], 'Variant must be RFC 4122 (8,9,a,b)');
    }
}
