<?php

require_once dirname(__FILE__) . '/../../Phpmodbus/ModbusMasterUdp.php';

// Received bytes interpreting 3 REAL values (6 words)
$data = array( // String "Hello word!"
    0x48, //H
    0x65, //e
    0x6c, //l
    0x6c, //l
    0x6f, //o
    0x20, //
    0x77, //w
    0x6f, //o
    0x72, //r
    0x6c, //l
    0x64, //d
    0x21, //!
    0x00, //\0
    0x61, //a
    0x61  //a
);

// Print string interpretation of the values
echo PhpType::bytes2string($data) . "<br>";
echo PhpType::bytes2string($data, true) . "<br>";

?>
