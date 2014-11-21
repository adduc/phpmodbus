phpmodbus
=========
This project deals with an implementation of the basic functionality of the Modbus TCP and UDP based protocol using PHP.
It's a copy of the releases from the project page over at [Google Code](https://code.google.com/p/phpmodbus/) with
composer support added.

Features
--------

* Modbus master
    * FC1 - Read coils
    * FC2 - Read input discretes
    * FC3 - Read holding registers
    * FC4 - Read holding input registers
    * FC5 - Write single coil
    * FC6 - Write single register
    * FC15 - Write multiple coils
    * FC16 - Write multiple registers
    * FC22 - Mask Write register
    * FC23 - Read/Write multiple registers

Example
-------

```php
 // Modbus master UDP
 $modbus = new ModbusMaster("192.168.1.1", "UDP");
 // Read multiple registers
 try {
     $recData = $modbus->readMultipleRegisters(0, 12288, 5);
 }
 catch (Exception $e) {
     // Print error information if any
     echo $modbus;
     echo $e;
     exit;
 }
 // Print data in string format
 echo PhpType::bytes2string($recData);
```

For more see [documentation][] or [FAQ][].

[documentation]: https://code.google.com/p/phpmodbus/downloads/list
[FAQ]: https://code.google.com/p/phpmodbus/wiki/FAQ
