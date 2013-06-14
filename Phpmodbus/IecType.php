<?php
/**
 * Phpmodbus Copyright (c) 2004, 2009 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)
 *  
 * This source file is subject to the "PhpModbus license" that is bundled
 * with this package in the file license.txt. 
 * 
 * @author Jan Krakora
 * @copyright Copyright (c) 2004, 2009 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)
 * @license PhpModbus license 
 * @category Phpmodbus
 * @package Phpmodbus
 * @version $id$
 */

/**
 * IecType
 *   
 * The class includes set of IEC-1131 data type functions that converts a PHP 
 * data types to a IEC data type.
 *
 * @author Jan Krakora
 * @copyright  Copyright (c) 2004, 2009 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)      
 * @package Phpmodbus  
 */
class IecType {

  /**
   * iecBYTE
   *  
   * Converts a value to IEC-1131 BYTE data type
   * 
   * @param value value from 0 to 255
   * @return value IEC BYTE data type
   *  
   */   
  function iecBYTE($value){
    return chr($value & 0xFF);
  }
  
  /**
   * iecINT
   *  
   * Converts a value to IEC-1131 INT data type
   * 
   * @param value value to be converted
   * @return value IEC-1131 INT data type    
   *  
   */ 
  function iecINT($value){
    return self::iecBYTE(($value >> 8) & 0x00FF) . 
      self::iecBYTE(($value & 0x00FF));
  }
  
  /**
   * iecDINT
   *  
   * Converts a value to IEC-1131 DINT data type
   * 
   * @param value value to be converted
   * @param value endianness defines endian codding (little endian == 0, big endian == 1)  
   * @return value IEC-1131 INT data type
   *  
   */
  function iecDINT($value, $endianness = 0){
    // result with right endianness
    return self::endianness($value, $endianness);
  }
  
  /**
   * iecREAL
   *  
   * Converts a value to IEC-1131 REAL data type. The function uses function  @use float2iecReal. 
   * 
   * @param value value to be converted
   * @param value endianness defines endian codding (little endian == 0, big endian == 1) 
   * @return value IEC-1131 REAL data type
   */
  function iecREAL($value, $endianness = 0){
    // iecREAL representation
    $real = self::float2iecReal($value);
    // result with right endianness
    return self::endianness($real, $endianness);
  }
  
  /**
   * float2iecReal
   *  
   * This function converts float value to IEC-1131 REAL single precision form.
   * 
   * For more see [{@link http://en.wikipedia.org/wiki/Single_precision Single precision on Wiki}] or
   * [{@link http://de.php.net/manual/en/function.base-convert.php PHP base_convert function commentary}, Todd Stokes @ Georgia Tech 21-Nov-2007]*
   *    
   * @param float value to be converted
   * @return value IEC REAL data type 
   */   
  private function float2iecReal($value){
    $bias = 128;
  	$cnst = 281;		// 1 (carry bit) + 127 + 1 + 126 + 24 + 2 (round bits)
  	$two_power_x = array(1, 2, 4, 8, 16, 32, 64, 128, 256, 512, 1024, 2048, 
      4096, 8192, 16384, 32768, 65536, 131072, 262144, 524288, 1048576, 
      2097152, 4194304);    
    //convert and seperate input to integer and decimal parts
    $val = abs($value);
    $intpart = floor($val);
    $decpart = $val - $intpart;  
    //convert integer part
  	for ($i=0;$i<$cnst;$i++) $real_significand_bin[$i] = 0;
    $i = $bias;
    while ((($intpart / 2) != 0) && ($i >= 0))
    {
      $real_significand_bin[$i] = $intpart % 2;
      if (($intpart % 2) == 0) $intpart = $intpart / 2;
        else $intpart = $intpart / 2 - 0.5;
      $i -= 1;
    }  
    //convert decimal part
    $i = $bias+1;
    while (($decpart > 0) && ($i < $cnst))
    {
      $decpart *= 2;
      if ($decpart >= 1) {
        $real_significand_bin[$i] = 1;
        $decpart --;
        $i++;
      }
      else 
      {
        $real_significand_bin[i] = 0;
        $i++;
      }
    }  
    //obtain exponent value
    $i = 0;  
    //find most significant bit of significand
    while (($i < $cnst) && ($real_significand_bin[$i] != 1)) $i++;
    //
  	$index_exp = $i;
    $real_exponent = 128 - $index_exp;
  	if ($real_exponent < -126) return 0;
  	if (($real_exponent > 127)&&($real_float>0)) return 0x7F7FFFFF;
  	if (($real_exponent > 127)&&($real_float<0)) return 0xFF7FFFFF;
  	for ($i=0; $i<23; $i++)
  		$real_significand = $real_significand + $real_significand_bin[$index_exp+1+$i] * $two_power_x[22-$i];
  	// return
  	if ($value<0) $w = 0x80000000 + ($real_significand & 0x7FFFFF) + ((($real_exponent+127)<<23) & 0x7F800000);
  	else $w = ($real_significand & 0x7FFFFF) + ((($real_exponent+127)<<23) & 0x7F800000);
  	return $w;
  }
  
  /**
   * endianness
   *
   * Make endianess as required.
   * For more see http://en.wikipedia.org/wiki/Endianness
   *
   * @param int $value
   * @param bool $endianness
   * @return int
   */
  private function endianness($value, $endianness = 0){
    if ($endianness == 0)
      return
        self::iecBYTE(($value >> 8) & 0x000000FF) .
        self::iecBYTE(($value & 0x000000FF)) .        
        self::iecBYTE(($value >> 24) & 0x000000FF) .
        self::iecBYTE(($value >> 16) & 0x000000FF);
    else
      return
        self::iecBYTE(($value >> 24) & 0x000000FF) .
        self::iecBYTE(($value >> 16) & 0x000000FF) .
        self::iecBYTE(($value >> 8) & 0x000000FF) .
        self::iecBYTE(($value & 0x000000FF));
  } 
  
}
  
?>