<?php

namespace xpl\Utility\Conversion;

class Multiplier {
	const DECI = 'd';	// Tenth
	const CENTI = 'c';	// Hundredth
	const MILLI = 'm';	// Thousandth
	const MICRO = 'u';	// Millionth
	const NANO = 'n';	// Billionth

	const DEKA = 'da';	// Ten
	const HECTO = 'h';	// Hundred
	const KILO = 'k';	// Thousand
	const MEGA = 'M';	// Million
	const GIGA = 'G';	// Billion
	const TERA = 'T';	// Trillion
	// just semantics
	const MILLION = 'M';
	const BILLION = 'B';
}

class Mass {
	const GRAM = 'g';
	const OUNCE = 'oz';
	const POUND = 'lb';
	const TON = 't';
	const TONNE = 'tonne';
}

class Volume {
	const GALLON = 'gal';
	const IMPERIAL_GALLON = 'igal';
	const LITER = 'L';
	const BARREL = 'bbl';
	const CUBIC_INCH = 'in^3';
	const CUBIC_FOOT = 'ft^3';
	const CUBIC_METER = 'm^3';
}

class Length {
	const METER = 'm';
	const INCH = 'in';
	const FOOT = 'ft';
	const YARD = 'yd';
	const MILE = 'mi';
	const NAUTICAL_MILE = 'nmi';
}
