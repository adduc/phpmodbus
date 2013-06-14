<?php

require_once dirname(__FILE__) . './Phpmodbus/ModbusMasterUdp.php';

// Create Modbus object
$modbus = new ModbusMasterUdp("192.168.1.99");

// FC 3
$recData = $modbus->readMultipleRegisters(0, 12288, 6);

if(!$recData) {
  // Print error information if any
  echo "</br>Error:</br>" . $modbus->errstr . "</br>";
}

// Print status information
echo "</br>Status:</br>" . $modbus->status . "</br>";

// Print read data
echo "</br>Data:</br>"; print_r($recData); echo "</br>";
?>