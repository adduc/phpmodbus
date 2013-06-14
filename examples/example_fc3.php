<?php

require_once dirname(__FILE__) . '/../Phpmodbus/ModbusMasterUdp.php';

// Create Modbus object
$modbus = new ModbusMasterUdp("192.192.15.51");

try {
    // FC 3
    $recData = $modbus->readMultipleRegisters(0, 12288, 6);
}
catch (Exception $e) {
    // Print error information if any
    echo $modbus;
    echo $e;
    exit;
}

// Print status information
echo "</br>Status:</br>" . $modbus;

// Print read data
echo "</br>Data:</br>";
print_r($recData); 
echo "</br>";
?>