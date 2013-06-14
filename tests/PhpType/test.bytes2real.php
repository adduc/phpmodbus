<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting 3 REAL values (6 words)
$data = array( // 1000
  0 => 0,
  1 => 0,
  2 => 68,
  3 => 122,
  4 => 0,
  5 => 0,
  6 => 68,
  7 => 250,
  8 => 0,
  9 => 0,
  10 => 63,
  11 => 160,
);

$dword = array_chunk($data, 4);

// Print float interpretation of the real value 
echo PhpType::bytes2float($dword[0]) . "<br>";
echo PhpType::bytes2float($dword[1]) . "<br>";
echo PhpType::bytes2float($dword[2]) . "<br>";
?>