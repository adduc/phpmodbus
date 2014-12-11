<?php

require_once dirname(__FILE__) . '/../Phpmodbus/ModbusMaster.php';

// Create Modbus object
$modbus = new ModbusMaster("192.192.15.51", "UDP");

// Data to be writen
$bitValue = true;
$bitNumber = 2;
$andMask =  0xFFFF ^ pow(2, $bitNumber) ;
$orMask =  0x0000 ^ (pow(2, $bitNumber) * $bitValue ) ;

try {
    // FC22
    $modbus->maskWriteRegister(0, 12288, $andMask, $orMask);
}
catch (Exception $e) {
    // Print error information if any
    echo $modbus;
    echo $e;
    exit;
}

// Print status information
echo $modbus;

?>
