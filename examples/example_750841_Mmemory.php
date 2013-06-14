<?php

require_once dirname(__FILE__) . './Phpmodbus/ModbusMasterUdp.php';

// Create Modbus object
$ip = "192.168.1.99";
$modbus = new ModbusMasterUdp($ip);

// FC 3
$moduleId = 0;
$reference = 12288;
$mw0address = 12288;
$quantity = 6;
$recData = $modbus->readMultipleRegisters($moduleId, $reference, $quantity);
  
?>
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <title>WAGO 750-841 M-memory dump</title>
  </head>
  <body>
    <h1>Dump of M-memory from WAGO 750-84x series coupler.</h1>
    <ul>
      <li>PLC: 750-84x series</li>
      <li>IP: <?=$ip?></li>
      <li>Modbus module ID: <?=$moduleId?></li>
      <li>Modbus memory reference: <?=$reference?></li>
      <li>Modbus memory quantity: <?=$quantity?></li>
    </ul>
    
    <h2>M-memory dump</h2>
    
    <?php
    if(!$recData) {
          // Print error information if any
          echo "</br>Error:</br>" . $modbus->errstr . "</br>";
    } 
    else 
    {  
    ?>
    
    <table border="1px" width="400px">
      <tr>
        <td>Modbus address</td>
        <td>MWx</td>
        <td>value</td>
      </tr>
      <?php
        for($i=0;$i<count($recData);$i+=2){          
      ?>
      <tr>
        <td><?=$i+$reference?></td>
        <td>MW<?=($i + $reference - $mw0address)/2?></td>
        <td><?=($recData[$i] << 8)+ $recData[$i+1]?></td>
      </tr>
      <?php
        }
      ?>
    </table>
    
    <?php
    }
    ?>
    
    <h2>Modbus class status</h2>
    <?php
      echo $modbus->status;
    ?>
    
  </body>
</html> 
