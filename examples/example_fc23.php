<?php

require_once dirname(__FILE__) . '/../Phpmodbus/ModbusMasterUdp.php';

// Create Modbus object
$modbus = new ModbusMasterUdp("192.192.15.51");

// Data to be writen
$data = array(1000, 2000, 3.0);
$dataTypes = array("INT", "DINT", "REAL");

try {
    // FC23
    $recData = $modbus->readWriteRegisters(0, 12288, 6, 12288, $data, $dataTypes);
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