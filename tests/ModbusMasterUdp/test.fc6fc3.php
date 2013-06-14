<?php
require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';
require_once dirname(__FILE__) . '/../config.php';

// Create Modbus object
$modbus = new ModbusMasterUdp($test_host_ip);

// Data to be writen - INT
$data = array(-12345);
$dataTypes = array("INT");
// Write data - FC6
$modbus->writeSingleRegister(0, 12288, $data, $dataTypes);
// Read data - FC3
$recData = $modbus->readMultipleRegisters(0, 12288, 1);
print_r($recData);
