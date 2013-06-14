<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting Mixed values
$data = Array (
    "0" => 100, // 32098 (DINT)
    "1" => "e",
    "2" => 0,
    "3" => 0
);

// Print mixed values
try {
  echo PhpType::bytes2unsignedInt(array_slice($data, 0, 4));
} catch(Exception $e) {
  echo "Exception 'Data are not numeric'";
}
?>
