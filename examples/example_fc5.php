<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Create Modbus object
$modbus = new ModbusMasterUdp("192.192.15.51");

// Data to be writen - TRUE, FALSE
$data_true = array(TRUE);
$data_false = array(FALSE);

try {
    // Write single coil - FC5
    $modbus->writeSingleCoil(0, 12288, $data_true);
    $modbus->writeSingleCoil(0, 12289, $data_false);
    $modbus->writeSingleCoil(0, 12290, $data_true);
    $modbus->writeSingleCoil(0, 12291, $data_false);
}
catch (Exception $e) {
    // Print error information if any
    echo $modbus;
    echo $e;
    exit;
}
