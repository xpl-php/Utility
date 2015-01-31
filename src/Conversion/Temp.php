<?php

namespace xpl\Utility\Conversion;

use DomainException;

class Temp implements ConverterInterface
{	
	/**
	 * Convert a temperature to another unit.
	 * 
	 * @param number $quantity Temperature to convert given in degrees.
	 * @param string $from Given temperature unit: one of "C", "F", or "K"
	 * @param string $to Temperature unit to convert to.
	 * 
	 * @return string Temperature in new unit.
	 * 
	 * @throws DomainException if either temperature unit is unknown.
	 */
	public static function convert($quantity, $from, $to) {
		
		$to = strtoupper($to);
		
		switch(strtoupper($from)) {
				
			case 'F':
				
				switch($to) {
						
					case 'C' :
						// ($quantity - 32) * (5/9);
						return bcdiv(bcmul(bcsub($quantity, '32', '10'), '5', '10'), '9', '10');
					
					case 'K' :
						// return ($quantity - 32) * (5/9) + 273.15;
						return bcadd(bcdiv(bcmul(bcsub($quantity, '32', '10'), '5', '10'), '9', '10'), '273.15', '10');
						
					default:
						break 2;
				}
			
			case 'C':
				
				switch($to) {
				
					case 'F' :
						#return ($quantity * (9/5)) + 32;
						return bcadd(bcdiv(bcmul($quantity, '9', '10'), '5', '10'), '32', '10');
						
					case 'K' :
						#return $quantity + 273.15;
						return bcadd($quantity, '273.15', '10');
						
					default:
						break 2;
				}
			
			case 'K' :
				
				switch($to) {
				
					case 'C' :
						#return $quantity - 273.15;
						return bcsub($quantity, '273.15', '10');
					
					case 'F' :
						#return ($quantity - 273.15) * (9/5) + 32;
						return bcadd(bcmul(bcsub($quantity, '273.15', '10'), bcdiv('9', '5', '10'), '10'), '32', '10');
						
					default:
						break 2;
				}
			
			default :
				throw new DomainException("Unknown temperature unit '$from'.");
		}
		
		throw new DomainException("Unknown temperature unit '$to'.");
	}
		
	
}
