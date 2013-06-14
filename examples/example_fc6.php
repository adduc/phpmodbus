<?php

require_once dirname(__FILE__) . '/../Phpmodbus/ModbusMaster.php';

// Create Modbus object
$modbus = new ModbusMaster("192.192.15.51", "UDP");

// Data to be writen
$data = array(-1000);
$dataTypes = array("INT");

try {
    // FC6
    $modbus->writeSingleRegister(0, 12288, $data, $dataTypes);
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