<?php

use PHPUnit\Framework\TestCase;

final class UuidV5Test extends TestCase
{
    public function testV5Format()
    {
        $uuid = \Nishadil\Uuid\Uuid::v5()
            ->withNamespace('6ba7b810-9dad-11d1-80b4-00c04fd430c8')
            ->withName('www.example.com')
            ->get();

        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-5[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $uuid,
            "UUID v5 should match RFC-4122 v5 pattern"
        );
    }

    public function testV5Deterministic()
    {
        $ns = '6ba7b810-9dad-11d1-80b4-00c04fd430c8'; // DNS namespace (RFC)
        $name = 'www.example.com';

        $nsHex = preg_replace('/[^0-9a-fA-F]/', '', $ns);
        $this->assertEquals(32, strlen($nsHex), 'Namespace must normalize to 32 hex chars for test');

        $nsBin = hex2bin($nsHex);
        $this->assertNotFalse($nsBin, 'Namespace hex must be valid');

        $raw = sha1($nsBin . $name, true); // 20 bytes
        $raw = substr($raw, 0, 16);

        // set version = 5
        $raw[6] = chr((ord($raw[6]) & 0x0f) | (5 << 4));
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

        $prep = [
            'NISHADIL_UUID_VERSION'   => 5,
            'NISHADIL_UUID_NAMESPACE' => $ns,
            'NISHADIL_UUID_NAME'      => $name,
        ];
        $actual = \Nishadil\Uuid\Factory::generate($prep);

        $this->assertEquals(strtolower($expected), strtolower($actual), 'v5 UUID must be deterministic and match SHA1-derived value');
    }

    public function testVersionAndVariantBits()
    {
        $prep = [
            'NISHADIL_UUID_VERSION'   => 5,
            'NISHADIL_UUID_NAMESPACE' => '6ba7b810-9dad-11d1-80b4-00c04fd430c8',
            'NISHADIL_UUID_NAME'      => 'www.example.com',
        ];

        $uuid = \Nishadil\Uuid\Factory::generate($prep);
        $parts = explode('-', $uuid);
        $this->assertCount(5, $parts);

        $this->assertMatchesRegularExpression('/^5[0-9a-f]{3}$/i', $parts[2], 'Version nibble must be 5 for v5');
        $this->assertMatchesRegularExpression('/^[89ab][0-9a-f]{3}$/i', $parts[3], 'Variant must be RFC 4122 (8,9,a,b)');
    }
}
