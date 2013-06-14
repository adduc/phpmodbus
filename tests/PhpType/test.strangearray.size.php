<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting Mixed values
$data = Array (
    "0" => 100, // 32098 (DINT)
    "1" => 2,
    "2" => 0,
    "3" => 0,
    "4" => 100, // 32098 (DINT)
    "5" => 2
);

// Print mixed values
try {
  echo PhpType::bytes2unsignedInt(array_slice($data, 0, 1)) . "<br>";
} catch(Exception $e) {
  echo "Exception 'Data are not in array 2 or 4 bytes'". "<br>";
}
try {
  echo PhpType::bytes2unsignedInt(array_slice($data, 0, 2)). "<br>";
} catch(Exception $e) {
  echo "Exception 'Data are not in array 2 or 4 bytes'". "<br>";
}
try {
  echo PhpType::bytes2unsignedInt(array_slice($data, 0, 3)). "<br>";
} catch(Exception $e) {
  echo "Exception 'Data are not in array 2 or 4 bytes'". "<br>";
}
try {
  echo PhpType::bytes2unsignedInt(array_slice($data, 0, 4)). "<br>";
} catch(Exception $e) {
  echo "Exception 'Data are not in array 2 or 4 bytes'". "<br>";
}
try {
  echo PhpType::bytes2unsignedInt(array_slice($data, 0, 5)). "<br>";
} catch(Exception $e) {
  echo "Exception 'Data are not in array 2 or 4 bytes'". "<br>";
}
?>