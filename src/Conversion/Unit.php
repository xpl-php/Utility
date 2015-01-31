<?php

namespace xpl\Utility\Conversion;

require_once 'definitions.php';

class Unit implements ConverterInterface
{
	
	/**
	 * Unit prefixes and multipliers.
	 * 
	 * @var array
	 */
	protected static $prefixes = array(

		Multiplier::DECI => '0.1',
		Multiplier::CENTI => '0.01',
		Multiplier::MILLI => '0.001',
		Multiplier::MICRO => '0.0001',
		Multiplier::NANO => '0.00001',
		
		Multiplier::DEKA => '10',
		Multiplier::HECTO => '100',
		Multiplier::KILO => '1000',
		Multiplier::MEGA => '1000000',
		Multiplier::GIGA => '1000000000',
		
		Multiplier::MILLION => '1000000',
		Multiplier::BILLION => '1000000000',
		
		// e.g. mmbbl = million barrels
		'mm' => '1000000',
	);
	
	/**
	 * Unit names.
	 * 
	 * @var array
	 */
	protected static $names = array(
	
		Mass::GRAM => 'gram',
		Mass::OUNCE => 'ounce',
		Mass::POUND => 'pound',
		Mass::TON => 'ton',
		Mass::TONNE => 'tonne',
		
		Volume::GALLON => 'gallon',
		Volume::IMPERIAL_GALLON => 'imperial gallon',
		Volume::LITER => 'liter',
		Volume::BARREL => 'barrel',
		Volume::CUBIC_INCH => 'cubic inch',
		Volume::CUBIC_FOOT => 'cubic foot',
		Volume::CUBIC_METER => 'cubic meter',
		
		Length::METER => 'meter',
		Length::INCH => 'inch',
		Length::FOOT => 'foot',
		Length::YARD => 'yard',
		Length::MILE => 'mile',
		
		'ppm' => 'parts per million',
	);
	
	/**
	 * Unit conversions.
	 * 
	 * Read like: "{Top-level array key} per 1 {nested array key}"
	 * 
	 * @var array
	 */
	protected static $convert = array(
		
		// Mass
		Mass::GRAM => array(
			Mass::POUND => '453.592', // grams per pound
			Mass::TON => '907185', // grams per ton
			Mass::TONNE => '1000000',
		),
		Mass::OUNCE => array(
			Mass::POUND => '16', // ounces per pound
			Mass::TON => '32000', // etc...
			Mass::TONNE => '35274',
		),
		Mass::POUND => array(
			Mass::TON => '2000',
			Mass::TONNE => '2204.62',
		),
		Mass::TON => array(
			Mass::TONNE => '1.10231',
		),
		
		// Volume
		Volume::GALLON => array(
			Volume::IMPERIAL_GALLON => '0.832674',
			Volume::LITER => '3.78541',
			Volume::BARREL => '42',
			Volume::CUBIC_INCH => '0.004329',
			Volume::CUBIC_FOOT => '0.133681',
			Volume::CUBIC_METER => '264.172',
		),
		Volume::LITER => array(
			Volume::IMPERIAL_GALLON => '4.54609',
		),
		Volume::CUBIC_INCH => array(
			Volume::CUBIC_FOOT => '1728',
			Volume::CUBIC_METER => '61023.7',
		),
		Volume::CUBIC_FOOT => array(
			Volume::CUBIC_METER => '35.3147',
		),
		
		// Length
		Length::INCH => array(
			Length::FOOT => '12',
			Length::YARD => '36',
			Length::MILE => '63360',
			Length::NAUTICAL_MILE => '72913.4',
		),
		Length::FOOT => array(
			Length::YARD => '3',
			Length::MILE => '5280',
			Length::NAUTICAL_MILE => '6076.12',
		),
		Length::METER => array(
			Length::INCH => '0.0254',
			Length::FOOT => '0.3048',
			Length::YARD => '0.9144',
			Length::MILE => '1609.34',
			Length::NAUTICAL_MILE => '1852',
		),
	);
	
	/**
	 * Converts a quantity from one unit to another.
	 * 
	 * @param number $quantity Number of units.
	 * @param string $from Unit in which quantity is given.
	 * @param string $to Unit to convert quantity to.
	 * @return float Quantity in new unit, or null if fail.
	 */
	public static function convert($quantity, $from, $to) {
		
		// Default multipliers = 1
		$fMult = $tMult = '1';
		
		// Remove prefixes and convert multipliers
		$from = static::parse($from, $fMult);
		$to = static::parse($to, $tMult);
		
		// Get conversion factor for base units
		$factor = static::getConversionFactor($from, $to);
		
		if (null === $factor) {
			// No conversion factor
			return null;
		}
		
		// New value = Quantity x Factor x Multiplier Ratio
		$newQty = bcmul($factor, $quantity, '10');
		$mRatio = bcdiv($fMult, $tMult, '10');
		
		return bcmul($newQty, $mRatio, '10');
	}
	
	/**
	 * Returns conversion factor for a given pair of units.
	 * 
	 * @param string $from Unit to convert from.
	 * @param string $to Unit to convert to.
	 * @return float Conversion factor.
	 */
	public static function getConversionFactor($from, $to) {
		
		if (isset(static::$convert[$to])) {
			// Direct conversion factor from X to Y
			if (isset(static::$convert[$to][$from])) {
				return (string) static::$convert[$to][$from];
			}
		}
			
		if (isset(static::$convert[$from])) {
			// Inverse conversion factor from Y to X
			// X-to-Y conversion factor = (1 / Y-to-X)
			if (isset(static::$convert[$from][$to])){
				return bcdiv('1', (string) static::$convert[$from][$to], '10');
			}
		}
		
		return null;
	}
	
	/**
	 * Returns a unit's name.
	 * 
	 * @param string $unit Unit
	 * @return string Name
	 */
	public static function getName($unit) {
		return isset(static::$names[$unit]) ? static::$names[$unit] : null;
	}
	
	/**
	 * Set a conversion factor.
	 * 
	 * @param string $base_unit Abbreviation for base unit.
	 * @param string $convert_unit Abbreviation for unit converting to.
	 * @param number $conversion_factor The number of $convert_unit's per $base_unit.
	 * @return void
	 */
	public static function setConversionFactor($base_unit, $convert_unit, $conversion_factor) {
			
		if (! is_numeric($conversion_factor)) {
			throw new \InvalidArgumentException("Conversion factor must be numeric, given: ".gettype($conversion_factor));
		}
		
		if (! isset(static::$convert[$convert_unit])) {
			static::$convert[$convert_unit] = array();
		}
		
		static::$convert[$convert_unit][$base_unit] = (string) $conversion_factor;
	}
	
	/**
	 * Parses a unit, extracting prefixes and returns the base unit.
	 * 
	 * @param string $unit Unit to parse, possibly with a prefix.
	 * @param int &$multiplier Multiplier corresponding to prefix, if any.
	 * @return string Base unit
	 */
	protected static function parse($unit, &$multiplier = 1) {
		
		// Parse prefixes if not a base unit
		if (! isset(static::$names[$unit])) {
				
			foreach(static::$prefixes as $prefix => $mult) {
				
				if  (0 === strpos($unit, $prefix)) {
					// Found prefix => set multiplier and return base unit
					$multiplier = $mult;
					return substr($unit, strlen($prefix));
				}
			}
		}
		
		// Base unit given or no multiplier found
		return $unit; 
	}
	
}
