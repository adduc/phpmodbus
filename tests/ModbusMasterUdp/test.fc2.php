<?php
require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';
require_once dirname(__FILE__) . '/../config.php';

// Create Modbus object
$modbus = new ModbusMasterUdp($test_host_ip);

// Test requirements
echo "Test should pass when %IX0.0==FALSE and %IX0.1==TRUE\n";

// Read input discretes - FC 2
$recData = $modbus->readInputDiscretes(0, 0, 2);

var_dump($recData);