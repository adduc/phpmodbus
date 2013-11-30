<?php
require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';
require_once dirname(__FILE__) . '/../config.php';

// Create Modbus object
$modbus = new ModbusMasterUdp($test_host_ip);

// Data to be writen - TRUE, FALSE
$data_true = array(TRUE);
$data_false = array(FALSE);

// Reset target WORD
$modbus->writeSingleRegister(0, 12288, array(0), array('WORD'));

// Write single coil - FC5
$modbus->writeSingleCoil(0, 12288, $data_true);
$modbus->writeSingleCoil(0, 12289, $data_false);
$modbus->writeSingleCoil(0, 12290, $data_true);
$modbus->writeSingleCoil(0, 12291, $data_false);

// Read data - FC3
$recData = $modbus->readMultipleRegisters(0, 12288, 1);
print_r($recData);
