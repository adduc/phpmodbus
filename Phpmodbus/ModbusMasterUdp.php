<?php
/**
 * Phpmodbus Copyright (c) 2004, 2009 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)
 *  
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt.
 *   
 *
 * @copyright  Copyright (c) 2004, 2009 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)
 * @license PhpModbus license 
 * @category Phpmodbus
 * @tutorial Phpmodbus.pkg 
 * @package Phpmodbus 
 * @version $id$
 *  
 */

require_once dirname(__FILE__) . '/IecType.php';
require_once dirname(__FILE__) . '/PhpType.php'; 

/**
 * ModbusMasterUdp
 *
 * This class deals with the MODBUS master using UDP stack.
 *  
 * Implemented MODBUS functions:
 *   - FC  3: read multiple registers
 *   - FC 16: write multiple registers
 *   - FC 23: read write registers
 *   
 * @author Jan Krakora
 * @copyright  Copyright (c) 2004, 2009 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)  
 * @package Phpmodbus  
 *
 */
class ModbusMasterUdp {
  var $sock;
  var $port = "502";
  var $host = "192.168.1.1";  
  var $errstr;
  var $status;
  var $timeout_sec = 5; // 5 sec
  var $endianess = 0; // defines endian codding (little endian == 0, big endian == 1) 
  
  /**
   * Modbus
   *
   * This is the constructor that defines {@link $host} IP address of the object. 
   *     
   * @param String $host An IP address of a Modbus TCP device. E.g. "192.168.1.1".
   */         
  function ModbusMasterUdp($host){
    $this->host = $host;
  }

  /**
   * connect
   *
   * Connect the socket
   *
   * @return bool
   */
  private function connect(){
    // UDP socket
    $this->sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);    
    // connect
    $result = @socket_connect($this->sock, $this->host, $this->port);
    if ($result === false) {
        $this->errstr .= "socket_connect() failed.</br>Reason: ($result) " . 
            socket_strerror(socket_last_error($this->sock));
        return false;
    } else {
        $this->status .= "Connected</br>";
        return true;        
    }    
  }

  /**
   * disconnect
   *
   * Disconnect the socket
   */
  private function disconnect(){    
    socket_close($this->sock);
    $this->status .= "Disconnected</br>";
  }

  /**
   * send
   *
   * Send the packet via Modbus
   *
   * @param string $packet
   */
  private function send($packet){
    socket_write($this->sock, $packet, strlen($packet));  
    $this->status .= "Send</br>";  
  }

  /**
   * rec
   *
   * Receive data from the socket
   *
   * @return bool
   */
  private function rec(){
    socket_set_nonblock($this->sock);
    $readsocks[] = $this->sock;     
    $writesocks = NULL;
    $exceptsocks = NULL;
    $rec = "";
    $lastAccess = time();
    while (socket_select($readsocks, 
            $writesocks, 
            $exceptsocks,
            0, 
            300000) !== FALSE) {
            $this->status .= "Wait received data</br>";            
        if (in_array($this->sock, $readsocks)) {
            while (@socket_recv($this->sock, $rec, 2000, 0)) {
                $this->status .= "Received</br>";
                return $rec;
            }
            $lastAccess = time();
        } else {             
            if (time()-$lastAccess >= $this->timeout_sec) {                
                $this->errstr .= "Watchdog time expired [ " . 
                  $this->timeout_sec . " sec]!!! Connection to " . 
                  $this->host . " is not established.";
                return false;
            }
        }
        $readsocks[] = $this->sock;
    }
  } 
  
  /**
   * responseCode
   *
   * Check the Modbus response code
   *
   * @param string $packet
   * @return bool
   */
  private function responseCode($packet){    
    if(($packet[7] & 0x80) > 0) {
      $this->errstr .= "Modbus response error code:" . ord($packet[8]);
      return false;
    }
    else
    {      
      $this->status .= "Modbus response error code: NOERROR</br>";
      return true;
    }    
  }
  
  /**
   * readMultipleRegisters
   *
   * Modbus function FC 3(0x03) - Read Multiple Registers.
   * 
   * This function reads {@link $quantity} of Words (2 bytes) from reference 
   * {@link $referenceRead} of a memory of a Modbus device given by 
   * {@link $unitId}.
   *    
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $reference Reference in the device memory to read data (e.g. in device WAGO 750-841, memory MW0 starts at address 12288).
   * @param int $quantity Amounth of the data to be read from device.
   * @return false|Array Success flag or array of received data.
   */
  function readMultipleRegisters($unitId, $reference, $quantity){
    $this->errstr = "";
    $this->status = "readMultipleRegisters: START</br>";
    // connect
    if(!$this->connect())
      return false;
    // send FC 3    
    $packet = $this->readMultipleRegistersPacketBuilder($unitId, $reference, $quantity);
    $this->status .= $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    if(!$rpacket)
      return false;
    $this->status .= $this->printPacket($rpacket);
    // parse packet
    $receivedData = $this->readMultipleRegistersParser($rpacket);
    if(!$receivedData)
      return false;
    // disconnect
    $this->disconnect();
    $this->status .= "readMultipleRegisters: DONE</br>";    
    // return
    return $receivedData;
  }
  
  /**
   * fc3
   *
   * Alias to {@link readMultipleRegisters} method.
   *
   * @param int $unitId
   * @param int $reference
   * @param int $quantity
   * @return false|Array
   */
  function fc3($unitId, $reference, $quantity){
    return $this->readMultipleRegisters($unitId, $reference, $quantity);
  }

  /**
   * readMultipleRegistersPacketBuilder
   *
   * Packet FC 3 builder - read multiple registers
   *
   * @param int $unitId
   * @param int $reference
   * @param int $quantity
   * @return string
   */
  private function readMultipleRegistersPacketBuilder($unitId, $reference, $quantity){
    $dataLen = 0;
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(3);             // FC 3 = 3(0x03)
    // build body - read section    
    $buffer2 .= iecType::iecINT($reference);  // refnumber = 12288      
    $buffer2 .= iecType::iecINT($quantity);       // quantity
    $dataLen += 5;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID
    $buffer3 .= iecType::iecINT(0);               // protocol ID
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * readMultipleRegistersParser
   *
   * FC 3 response parser
   *
   * @param string $packet
   * @return array
   */
  private function readMultipleRegistersParser($packet){
    $data = array();
    // if not exception
    if(!$this->responseCode($packet))
      return false;
    // get data
    for($i=0;$i<ord($packet[8]);$i++){
      $data[$i] = ord($packet[9+$i]);
    }    
    return $data;
  }
  
  /**
   * writeMultipleRegister
   *
   * Modbus function FC16(0x10) - Write Multiple Register.
   *
   * This function writes {@link $data} array at {@link $reference} position of 
   * memory of a Modbus device given by {@link $unitId}.
   *
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $reference Reference in the device memory (e.g. in device WAGO 750-841, memory MW0 starts at address 12288)
   * @param array $data Array of values to be written.
   * @param array $dataTypes Array of types of values to be written. The array should consists of string "INT", "DINT" and "REAL".    
   * @return bool Success flag
   */       
  function writeMultipleRegister($unitId, $reference, $data, $dataTypes){
    $this->errstr = "";
    $this->status = "writeMultipleRegister: START</br>";
    // connect
    if(!$this->connect())
      return false;
    // send FC16    
    $packet = $this->writeMultipleRegisterPacketBuilder($unitId, $reference, $data, $dataTypes);
    $this->status .= $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    if(!$rpacket)
      return false;
    $this->status .= $this->printPacket($rpacket);    
    // parse packet
    if(!$this->writeMultipleRegisterParser($rpacket))
      return false;    
    // disconnect
    $this->disconnect();
    $this->status .= "writeMultipleRegister: DONE</br>";
    return true;
  }


  /**
   * fc16
   *
   * Alias to {@link writeMultipleRegister} method
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return bool
   */
  function fc16($unitId, $reference, $data, $dataTypes){    
    return $this->writeMultipleRegister($unitId, $reference, $data, $dataTypes);
  }


  /**
   * writeMultipleRegisterPacketBuilder
   *
   * Packet builder FC16 - WRITE multiple register
   *     e.g.: 4dd90000000d0010300000030603e807d00bb8
   *
   * @param int $unitId
   * @param int $reference
   * @param array $data
   * @param array $dataTypes
   * @return string
   */
  private function writeMultipleRegisterPacketBuilder($unitId, $reference, $data, $dataTypes){
    $dataLen = 0;        
    // build data section
    $buffer1 = "";
    foreach($data as $key=>$dataitem) {
      if($dataTypes[$key]=="INT"){
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
      elseif($dataTypes[$key]=="DINT"){
        $buffer1 .= iecType::iecDINT($dataitem, $endianess);   // register values x
        $dataLen += 4;
      }
      elseif($dataTypes[$key]=="REAL") {
        $buffer1 .= iecType::iecREAL($dataitem, $endianess);   // register values x        
        $dataLen += 4;
      }       
      else{
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
    }
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(16);             // FC 16 = 16(0x10)
    $buffer2 .= iecType::iecINT($reference);      // refnumber = 12288      
    $buffer2 .= iecType::iecINT($dataLen/2);        // word count      
    $buffer2 .= iecType::iecBYTE($dataLen);     // byte count
    $dataLen += 6;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID    
    $buffer3 .= iecType::iecINT(0);               // protocol ID    
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght    
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID    
    
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  
  /**
   * writeMultipleRegisterParser
   *
   * FC16 response parser
   *
   * @param string $packet
   * @return bool
   */
  private function writeMultipleRegisterParser($packet){
    if(!$this->responseCode($rpacket))
      return false;
    return true;
  }
  
  /**
   * readWriteRegisters
   *
   * Modbus function FC23(0x17) - Read Write Registers.
   * 
   * This function writes {@link $data} array at reference {@link $referenceWrite} 
   * position of memory of a Modbus device given by {@link $unitId}. Simultanously, 
   * it returns {@link $quantity} of Words (2 bytes) from reference {@link $referenceRead}.
   *
   *
   * @param int $unitId usually ID of Modbus device 
   * @param int $referenceRead Reference in the device memory to read data (e.g. in device WAGO 750-841, memory MW0 starts at address 12288).
   * @param int $quantity Amounth of the data to be read from device.   
   * @param int $referenceWrite Reference in the device memory to write data.
   * @param array $data Array of values to be written.
   * @param array $dataTypes Array of types of values to be written. The array should consists of string "INT", "DINT" and "REAL".   
   * @return false|Array Success flag or array of data.
   */
  function readWriteRegisters($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes){
    $this->errstr = "";
    $this->status = "readWriteRegisters: START</br>";
    // connect
    if(!$this->connect())
      return false;
    // send FC23    
    $packet = $this->readWriteRegistersPacketBuilder($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes);
    $this->status .= $this->printPacket($packet);    
    $this->send($packet);
    // receive response
    $rpacket = $this->rec();
    if(!$rpacket)
      return false;
    $this->status .= $this->printPacket($rpacket);
    // parse packet
    $receivedData = $this->readWriteRegistersParser($rpacket); 
    if(!$receivedData)
      return false;
    // disconnect
    $this->disconnect();
    $this->status .= "writeMultipleRegister: DONE</br>";    
    // return
    return $receivedData;
  }
  
  /**
   * fc23
   *
   * Alias to {@link readWriteRegisters} method.
   *
   * @param int $unitId
   * @param int $referenceRead
   * @param int $quantity
   * @param int $referenceWrite
   * @param array $data
   * @param array $dataTypes
   * @return false|Array
   */
  function fc23($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes){
    return $this->readWriteRegisters($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes);
  }
  
  /**
   * readWriteRegistersPacketBuilder
   *
   * Packet FC23 builder - READ WRITE registers
   *
   *
   * @param int $unitId
   * @param int $referenceRead
   * @param int $quantity
   * @param int $referenceWrite
   * @param array $data
   * @param array $dataTypes
   * @return string
   */
  private function readWriteRegistersPacketBuilder($unitId, $referenceRead, $quantity, $referenceWrite, $data, $dataTypes){
    $dataLen = 0;        
    // build data section
    $buffer1 = "";
    foreach($data as $key => $dataitem) {
      if($dataTypes[$key]=="INT"){
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
      elseif($dataTypes[$key]=="DINT"){
        $buffer1 .= iecType::iecDINT($dataitem, $endianess);   // register values x
        $dataLen += 4;
      }
      elseif($dataTypes[$key]=="REAL") {
        $buffer1 .= iecType::iecREAL($dataitem, $endianess);   // register values x        
        $dataLen += 4;
      }       
      else{
        $buffer1 .= iecType::iecINT($dataitem);   // register values x
        $dataLen += 2;
      }
    }
    // build body
    $buffer2 = "";
    $buffer2 .= iecType::iecBYTE(23);             // FC 23 = 23(0x17)
    // build body - read section    
    $buffer2 .= iecType::iecINT($referenceRead);  // refnumber = 12288      
    $buffer2 .= iecType::iecINT($quantity);       // quantity
    // build body - write section    
    $buffer2 .= iecType::iecINT($referenceWrite); // refnumber = 12288      
    $buffer2 .= iecType::iecINT($dataLen/2);      // word count      
    $buffer2 .= iecType::iecBYTE($dataLen);       // byte count
    $dataLen += 10;
    // build header
    $buffer3 = '';
    $buffer3 .= iecType::iecINT(rand(0,65000));   // transaction ID    
    $buffer3 .= iecType::iecINT(0);               // protocol ID    
    $buffer3 .= iecType::iecINT($dataLen + 1);    // lenght    
    $buffer3 .= iecType::iecBYTE($unitId);        //unit ID    
    
    // return packet string
    return $buffer3. $buffer2. $buffer1;
  }
  

  /**
   * readWriteRegistersParser
   *
   * FC23 response parser
   *
   * @param string $packet
   * @return array
   */
  private function readWriteRegistersParser($packet){
    $data = array();
    // if not exception
    if(!$this->responseCode($packet))
      return false;
    // get data
    for($i=0;$i<ord($packet[8]);$i++){
      $data[$i] = ord($packet[9+$i]);
    }    
    return $data;
  }

  /**
   * byte2hex
   *
   * Parse data and get it to the Hex form
   *
   * @param char $value
   * @return string
   */
  private function byte2hex($value){
    $h = dechex(($value >> 4) & 0x0F);
    $l = dechex($value & 0x0F);
    return "$h$l";
  }

  /**
   * printPacket
   *
   * Print whole packet in the hex form
   *
   * @param string $packet
   * @return string
   */
  private function printPacket($packet){
    $str = "";   
    $str .= "Packet: "; 
    for($i=0;$i<strlen($packet);$i++){
      $str .= $this->byte2hex(ord($packet[$i]));
    }
    $str .= "</br>";
    return $str;
  }
}

?>