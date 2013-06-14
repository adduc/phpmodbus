<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting Mixed values
$data = Array (
    "0" => 0, 
    "1" => 1,
    "2" => -1,
    "3" => pow(2,31)-1,
    "4" => -pow(2,31)
);

function byte2hex($value){
  $h = dechex(($value >> 4) & 0x0F);
  $l = dechex($value & 0x0F);
  return "$h$l";
}
  
function printPacket($packet){
  $str = "";   
  $str .= "Packet: "; 
  for($i=0;$i<strlen($packet);$i++){    
    $str .= byte2hex(ord($packet[$i]));
    if($i % 2)
      $str .= "_";
  }
  $str .= "</br>";
  return $str;
}

echo "Endianing off <hr>";
// Print mixed values
for($i=0;$i<count($data);$i++) {
  echo $data[$i] . " --> ";
  $v = IecType::iecDINT($data[$i], 0);
  echo printPacket($v);
  "<br>";
}

echo "Endianing on <hr>";
// Print mixed values
for($i=0;$i<count($data);$i++) {
  echo $data[$i] . " --> ";
  $v = IecType::iecDINT($data[$i], 1);
  echo printPacket($v);
  "<br>";
}
 
?>