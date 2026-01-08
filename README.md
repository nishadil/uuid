# ![nishadil/uuid](https://avatars.githubusercontent.com/u/3072416?s=25&v=4) nishadil/uuid

<p align="center">
    <strong>A PHP library for generating universally unique identifiers (UUID).</strong>
</p>



## What is a UUID?

A UUID _(short for Universally Unique IDentifier)_ ​​is a 36-character alphanumeric string that can be used to identify information. 
For example, they are commonly used to identify rows of data in database tables, with each row assigned a specific UUID.

Here is an example of a UUID: `acde070d-8c4c-4f0d-9d8a-162843c10333`

One reason UUIDs are so widely used is that they are likely to be globally unique. In other words, not only row UUIDs are unique within a row. A database table is probably the only row with that UUID on any system.

_(Technically it's not impossible that the same UUID you generate could be used elsewhere, but it's highly unlikely as there are 340,282,366,920,938,463,463,374,607,431,768,211,456 possible different UUIDs.)_




## UUIDs version status

| Versions | Status | Info |
| ------ | ------ | ------ |
| v1 | `working` | UUIDs using date-time and MAC address |
| v2 | `working` | UUIDs using date-time and MAC address, DCE security version. |
| v3 | `working` | UUIDs based on the MD5 hash of some data. |
| v4 | `working` | UUIDs with random data. |
| v5 | `working` | UUIDs based on the SHA1 hash of some data. |
| v6 | `working` | UUIDs using a timestamp and monotonic counter. |
| v7 | `working` | UUIDs using a Unix timestamp. |
| v8 | `working` | UUIDs using user-defined data. |




## Installation

This library can be installed using [Composer][]. To install, please use following command

```bash
composer require nishadil/uuid
```


## How to use

Autoload the library once:

```php
<?php
require __DIR__.'/vendor/autoload.php';

use Nishadil\Uuid\Uuid;
```

v1 (time-based):

```php
$uuidV1 = Uuid::v1()->get();
```

v2 (DCE security, UID/GID domain):

```php
$uuidV2 = Uuid::v2()
    ->withLocalDomain('uid') // or 'gid'
    ->withLocalId(1000)
    ->get();
```

v3 (name-based, MD5):

```php
$uuidV3 = Uuid::v3()
    ->withNamespace('6ba7b810-9dad-11d1-80b4-00c04fd430c8')
    ->withName('www.example.com')
    ->get();
```

v4 (random):

```php
$uuidV4 = Uuid::v4()->get();
```

v5 (name-based, SHA1):

```php
$uuidV5 = Uuid::v5()
    ->withNamespace('6ba7b810-9dad-11d1-80b4-00c04fd430c8')
    ->withName('www.example.com')
    ->get();
```

v6 (reordered time-based):

```php
$uuidV6 = Uuid::v6()->get();
```

v7 (Unix time, ms):

```php
$uuidV7 = Uuid::v7()->get();
```

v8 (custom data, 16 bytes / 32 hex chars):

```php
$uuidV8 = Uuid::v8()
    ->withCustomHex('00112233445566778899aabbccddeeff')
    ->get();
```


## License

This library is licensed for use under the MIT License (MIT)
