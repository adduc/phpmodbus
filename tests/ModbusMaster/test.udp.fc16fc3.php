<?php
require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMaster.php';
require_once dirname(__FILE__) . '/../config.php';

// Create Modbus object
$modbus = new ModbusMaster($test_host_ip, "UDP");

// Data to be writen - BYTE
$data = array(0, 1, 1, pow(2,8)-1, pow(2,8)-1);
$dataTypes = array("BYTE", "BYTE", "BYTE", "BYTE", "BYTE");
// Write data - FC 16
$modbus->writeMultipleRegister(0, 12288, $data, $dataTypes);
// Read data - FC3
$recData = $modbus->readMultipleRegisters(0, 12288, 5);
print_r($recData);

// Data to be writen - INT
$data = array(0, 1, -1, pow(2,15)-1, -pow(2,15));
$dataTypes = array("INT", "INT", "INT", "INT", "INT");
// Write data - FC 16
$modbus->writeMultipleRegister(0, 12288, $data, $dataTypes);
// Read data - FC3
$recData = $modbus->readMultipleRegisters(0, 12288, 5);
print_r($recData);

// Data to be writen - DINT
$data = array(0, 1, -1, pow(2,31)-1, -pow(2,31));
$dataTypes = array("DINT", "DINT", "DINT", "DINT", "DINT");
// Write data - FC 16
$modbus->writeMultipleRegister(0, 12288, $data, $dataTypes);
// Read data - FC3
$recData = $modbus->readMultipleRegisters(0, 12288, 10);
print_r($recData);

// Data to be writen - REAL
$data = array(0, 1, -2, 1/3, 25);
$dataTypes = array("REAL", "REAL", "REAL", "REAL", "REAL");
// Write data - FC 16
$modbus->writeMultipleRegister(0, 12288, $data, $dataTypes);
// Read data - FC3
$recData = $modbus->readMultipleRegisters(0, 12288, 10);
print_r($recData);

?>