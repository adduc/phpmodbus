<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting DINT values
$data = array(
  0xFF, // -1
  0xFF,
  0xFF,
  0xFF,
  0, // 0
  0,
  0,
  0,
  0, // 1
  0x01,
  0,
  0,
  0, // minus max
  0,
  0x80,
  0x0,
  0xFF, // plus max
  0xFF,
  0x7F,
  0xFF,
);

$dword = array_chunk($data, 4);

// Print float interpretation of the real value 
foreach($dword as $value) {
  echo PhpType::bytes2signedInt($value) . "<br>";
}
?> 
