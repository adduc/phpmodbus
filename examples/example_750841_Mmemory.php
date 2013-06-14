<?php

require_once dirname(__FILE__) . '/../Phpmodbus/ModbusMaster.php';

// Create Modbus object
$ip = "192.192.15.51";
$modbus = new ModbusMaster($ip, "UDP");

try {
    // FC 3
    $moduleId = 0;
    $reference = 12288;
    $mw0address = 12288;
    $quantity = 6;
    $recData = $modbus->readMultipleRegisters($moduleId, $reference, $quantity);
}
catch (Exception $e) {
    echo $modbus;
    echo $e;
    exit;
}

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
            <li>IP: <?php echo $ip?></li>
            <li>Modbus module ID: <?php echo $moduleId?></li>
            <li>Modbus memory reference: <?php echo $reference?></li>
            <li>Modbus memory quantity: <?php echo $quantity?></li>
        </ul>

        <h2>M-memory dump</h2>

        <table border="1px" width="400px">
            <tr>
                <td>Modbus address</td>
                <td>MWx</td>
                <td>value</td>
            </tr>
            <?php
            for($i=0;$i<count($recData);$i+=2) {
                ?>
            <tr>
                <td><?php echo $i+$reference?></td>
                <td>MW<?php echo ($i + $reference - $mw0address)/2?></td>
                <td><?php echo ($recData[$i] << 8)+ $recData[$i+1]?></td>
            </tr>
                <?php
            }
            ?>
        </table>

        <h2>Modbus class status</h2>
        <?php
        echo $modbus;
        ?>

    </body>
</html> 
