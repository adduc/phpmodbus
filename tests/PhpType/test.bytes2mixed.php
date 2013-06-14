<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting Mixed values
$data = Array (
    "0" => 125, // 32098 (DINT)
    "1" => 98,
    "2" => 0,
    "3" => 0,
    "4" => 0,  // 0 (DINT)
    "5" => 0,
    "6" => 0,
    "7" => 0,
    "8" => 0,  // 0 (DINT)
    "9" => 0, 
    "10" => 0,
    "11" => 0,
    "12" => 255, // -1 (DINT)
    "13" => 255,
    "14" => 255,
    "15" => 255,
    "16" => 158, // -25000 (INT)
    "17" => 88,
    "18" => 97, // 25000 (INT)
    "19" => 168    
);

// Print mixed values
echo PhpType::bytes2unsignedInt(array_slice($data, 0, 4)) . "<br>";
echo PhpType::bytes2signedInt(array_slice($data, 4, 4)) . "<br>";
echo PhpType::bytes2signedInt(array_slice($data, 8, 4)) . "<br>";
echo PhpType::bytes2signedInt(array_slice($data, 12, 4)) . "<br>";
echo PhpType::bytes2signedInt(array_slice($data, 16, 2)) . "<br>";
echo PhpType::bytes2signedInt(array_slice($data, 18, 2)) . "<br>";
 
?>