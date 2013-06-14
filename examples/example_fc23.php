<?php

require_once dirname(__FILE__) . './Phpmodbus/ModbusMasterUdp.php';

// Create Modbus object
$modbus = new ModbusMasterUdp("192.168.1.99");

// Data to be writen
$data = array(1000, 2000, 3.0);
$dataTypes = array("INT", "DINT", "REAL");

// FC23
$recData = $modbus->readWriteRegisters(0, 12288, 6, 12288, $data, $dataTypes);

if(!$recData) {
  // Print error information if any
  echo "</br>Error:</br>" . $modbus->errstr . "</br>";
}

// Print status information
echo "</br>Status:</br>" . $modbus->status . "</br>";

// Print read data
echo "</br>Data:</br>"; print_r($recData); echo "</br>";

?>