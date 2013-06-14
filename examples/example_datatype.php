<?php

require_once dirname(__FILE__) . '/../Phpmodbus/ModbusMaster.php';

// Create Modbus object
$modbus = new ModbusMaster("192.192.15.51", "UDP");

try {
    // FC 3
    // read 10 words (20 bytes) from device ID=0, address=12288
    $recData = $modbus->readMultipleRegisters(0, 12288, 10);
}
catch (Exception $e) {
    // Print error information if any
    echo $modbus;
    echo $e;
    exit;
}

// Received data
echo "<h1>Received Data</h1>\n";
print_r($recData);

// Conversion
echo "<h2>32 bits types</h2>\n";
// Chunk the data array to set of 4 bytes
$values = array_chunk($recData, 4);

// Get float from REAL interpretation
echo "<h3>REAL to Float</h3>\n";
foreach($values as $bytes)
    echo PhpType::bytes2float($bytes) . "</br>";

// Get integer from DINT interpretation
echo "<h3>DINT to integer </h3>\n";
foreach($values as $bytes)
    echo PhpType::bytes2signedInt($bytes) . "</br>";

// Get integer of float from DINT interpretation
echo "<h3>DWORD to integer (or float) </h3>\n";
foreach($values as $bytes)
    echo PhpType::bytes2unsignedInt($bytes) . "</br>";

echo "<h2>16 bit types</h2>\n";
// Chunk the data array to set of 4 bytes
$values = array_chunk($recData, 2);

// Get signed integer from INT interpretation
echo "<h3>INT to integer </h3>\n";
foreach($values as $bytes)
    echo PhpType::bytes2signedInt($bytes) . "</br>";

// Get unsigned integer from WORD interpretation
echo "<h3>WORD to integer </h3>\n";
foreach($values as $bytes)
    echo PhpType::bytes2unsignedInt($bytes) . "</br>";

// Get string from STRING interpretation
echo "<h3>STRING to string </h3>\n";
echo PhpType::bytes2string($recData) . "</br>";
?>