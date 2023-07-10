# ![nishadil/uuid](https://avatars.githubusercontent.com/u/3072416?s=30&v=4) nishadil/uuid
<h1 align="center">
    nishadil/uuid
</h1>

<p align="center">
    <strong>A PHP library for generating universally unique identifiers (UUID).</strong>
</p>

## What is a UUID?

A UUID (short for Universally Unique IDentifier) ​​is a 36-character alphanumeric string that can be used to identify information. 
For example, they are commonly used to identify rows of data in database tables, with each row assigned a specific UUID.

Here is an example of a UUID: `acde070d-8c4c-4f0d-9d8a-162843c10333`

One reason UUIDs are so widely used is that they are likely to be globally unique. In other words, not only row UUIDs are unique within a row. A database table is probably the only row with that UUID on any system.

_(Technically it's not impossible that the same UUID you generate could be used elsewhere, but it's highly unlikely as there are 340,282,366,920,938,463,463,374,607,431,768,211,456 possible different UUIDs.)_

- Version 1: UUIDs using date-time and MAC address. `under development`
- Version 2: UUIDs using date-time and MAC address, DCE security version. `under development`
- Version 3: UUIDs based on the MD5 hash of some data. `under development`
- Version 4: UUIDs with random data. `under development`
- Version 5: UUIDs based on the SHA1 hash of some data. `under development`
- Version 6: UUIDs using a timestamp and monotonic counter. `under development`
- Version 7: UUIDs using a Unix timestamp. `under development`
- Version 8: UUIDs using user-defined data. `under development`



## Installation

This library can be installed using [Composer][]. To install, please use following command

```bash
composer require nishadil/uuid
```


## License

This library is licensed for use under the MIT License (MIT)