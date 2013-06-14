Copyright (c) 2004, 2009 Jan Krakora, Wago (http://www.wago.com)
All rights reserved.

Phpmodbus library
####################

Phpmodbus for PHP is a small and easy-to-use Modbus UDP master library. For more
see project at http://phpmodbus.googlecode.com

Release notes
===============

0.1 -> 0.2.r20
---------------
+ Added new class for conversion from received bytes to PHP data types (PhpType class)
+ Added new data conversion using PhpType example
+ Added new alias methods fc3, fc16 and fc23 (ModbusMasterUdp class)
* Fixed problems with the endianess when data written (IecType class)
* Improved commentaries for documentation
