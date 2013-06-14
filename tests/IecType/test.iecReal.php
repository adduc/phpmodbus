<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// http://en.wikipedia.org/wiki/Single_precision
$data = Array (
    "0" => 0, // -> 0000 0000
    "1" => 1, // -> 3f80 0000
    "2" => -2, // -> c000 0000
    "3" => 1/3, // -> 3eaa aaab
    "4" => 25 // -> 41c8 0000
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
  $str .= "<br>\n";
  return $str;
}

echo "Endianing off <hr>\n";
// Print mixed values
for($i=0;$i<count($data);$i++) {
  echo $data[$i] . " --> ";
  $v = IecType::iecREAL($data[$i], 0);
  echo printPacket($v);
  "<br>\n";
}

echo "Endianing on <hr>\n";
// Print mixed values
for($i=0;$i<count($data);$i++) {
  echo $data[$i] . " --> ";
  $v = IecType::iecREAL($data[$i], 1);
  echo printPacket($v);
  "<br>\n";
}
 
?>