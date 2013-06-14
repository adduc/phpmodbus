<?
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
 *  
 */

/**
 * PhpType
 *   
 * The class includes set of methods that convert the received Modbus data
 * (array of bytes) to the PHP data type, i.e. signed int, unsigned int and float.
 *
 * @author Jan Krakora
 * @copyright  Copyright (c) 2004, 2009 Jan Krakora, WAGO Kontakttechnik GmbH & Co. KG (http://www.wago.com)      
 * @package Phpmodbus  
 *  
 */
class PhpType {
  
  /**
   * bytes2float
   *
   * The function converts array of 4 bytes to float. The return value 
   * depends on order of the input bytes (endianning).
   *
   * @param array $values
   * @param bool $endianness
   * @return float
   */
  public static function bytes2float($values, $endianness = 0){
    $data = array();
    $real = 0;  
      
    // Set the array to correct form
    $data = self::checkData($values);    
    // Combine bytes
    $real = self::combineBytes($data, $endianness);
    // Convert the real value to float    
    return (float) self::real2float($real);
  }
  
  /**
   * bytes2signedInt
   *
   * The function converts array of 2 or 4 bytes to signed integer. 
   * The return value depends on order of the input bytes (endianning).
   *
   * @param array $values
   * @param bool $endianness
   * @return int
   */
  public static function bytes2signedInt($values, $endianness = 0){
    $data = array();
    $int = 0;
    // Set the array to correct form
    $data = self::checkData($values);    
    // Combine bytes
    $int = self::combineBytes($data, $endianness);
    // In the case of signed 2 byte value convert it to 4 byte one
    if ((count($values) == 2) && ((0x8000 & $int) > 0)){
      $int = 0xFFFF8000 | $int;
    }
    // Convert the value
    return (int) self::dword2signedInt($int);
  }
  
  /**
   * bytes2unsignedInt
   *
   * The function converts array of 2 or 4 bytes to unsigned integer.
   * The return value depends on order of the input bytes (endianning).
   *
   * @param array $values
   * @param bool $endianness
   * @return int|float
   */
  public static function bytes2unsignedInt($values, $endianness = 0){
    $data = array();
    $int = 0;
    // Set the array to correct form
    $data = self::checkData($values);
    // Combine bytes
    $int = self::combineBytes($data, $endianness);
    // Convert the value
    return self::dword2unsignedInt($int);
  }
  
  /**   
   * real2float
   *
   * This function converts a value in IEC-1131 REAL single precision form to float.
   *  
   * For more see [{@link http://en.wikipedia.org/wiki/Single_precision Single precision on Wiki}] or
   * [{@link http://de.php.net/manual/en/function.base-convert.php PHP base_convert function commentary}, Todd Stokes @ Georgia Tech 21-Nov-2007]
   * 
   * @param value value in IEC REAL data type to be converted
   * @return float float value 
   */
  private static function real2float($value){
    $two_pow_minus_x = array(
      1, 0.5, 0.25, 0.125, 0.0625, 0.03125, 0.015625, 
      0.0078125, 0.00390625, 0.001953125, 0.0009765625, 
      0.00048828125, 0.000244140625, 0.0001220703125, 
      0.00006103515625,	0.000030517578125, 0.0000152587890625, 
      0.00000762939453125, 0.000003814697265625, 0.0000019073486328125, 
      0.00000095367431640625, 0.000000476837158203125,
  		0.0000002384185791015625, 0.00000011920928955078125);
    // get sign, mantisa, exponent
  	$real_mantisa = $value & 0x7FFFFF | 0x800000; 
  	$real_exponent = ($value>>23) & 0xFF;
  	$real_sign = ($value>>31) & 0x01;
  	$bin_exponent = $real_exponent - 127;
  	// decode value
  	if (( $bin_exponent >= -126) && ($bin_exponent <= 127)) {
      // Mantissa decoding	
  		for ($i=0; $i<24; $i++) {		  
  		  if ($real_mantisa & 0x01)
  			  $val += $two_pow_minus_x[23-$i];
  			$real_mantisa = $real_mantisa >> 1;
  		}
      // Base
  		$val = $val * pow(2,$bin_exponent);
  		if (($real_sign == 1)) $val = -$val;
  	}	
  	return (float)$val;
  }
  
  /**
   * dword2signedInt
   *
   * Switch double word to signed integer
   *
   * @param int $value
   * @return int
   */
  private static function dword2signedInt($value){
    if ((0x80000000 & $value) != 0) {
      return -(0x7FFFFFFF & ~$value)-1;
    } else {
      return (0x7FFFFFFF & $value);
    }
  }
  
    /**
   * dword2signedInt
   *
   * Switch double word to unsigned integer
   *
   * @param int $value
   * @return int|float
   */
  private static function dword2unsignedInt($value){
    if ((0x80000000 & $value) != 0) {
      return ((float) (0x7FFFFFFF & $value)) + 2147483648;
    } else {
      return (int) (0x7FFFFFFF & $value);
    }
  }

  /**
   * checkData
   *
   * Check if the data variable is array, and check if the values are numeric
   *
   * @param int $data
   * @return int
   */
  private static function checkData($data){
    // Check the data
    if (!is_array($data)) {
        throw new Exception('The input data should be an array of bytes.');
    }
    // Check the values to be number - must be 
    if (!is_numeric($data[0]) || !is_numeric($data[1])) {
        throw new Exception('Data are not numeric.'); 
    }
    if (!is_numeric($data[2])) $data[2] = 0;
    if (!is_numeric($data[3])) $data[3] = 0;
    
    return $data;
  }
  
  /**
   * combineBytes
   *
   * Combine bytes together
   *
   * @param int $data
   * @param bool $endianness
   * @return int
   */
  private static function combineBytes($data, $endianness){
    $value = 0;
    // Combine bytes
    if ($endianness == 0)
      $value = (($data[3] & 0xFF)<<16) |
        (($data[2] & 0xFF)<<24) | 
        (($data[1] & 0xFF)) | 
        (($data[0] & 0xFF)<<8);
    else
      $value = (($data[3] & 0xFF)<<24) |
        (($data[2] & 0xFF)<<16) | 
        (($data[1] & 0xFF)<<8) | 
        (($data[0] & 0xFF));

    return $value;
  }
}
?>