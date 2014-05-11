<?php

namespace Phpf\Util;

use Phpf\Html\Attributes;

class Html {
	
	/**
	 * Parses a string of html attributes into a (potentially) nested array.
	 * 
	 * @param string $attrStr HTML attribute string to parse
	 * @return array Single or multi-dimensional assoc. array.
	 */
	public static function parseAttrs( $attrStr ){
		return Attributes::parse($attrStr);
	}
	
	/**
	 * Escapes an attribute value.
	 * 
	 * Note htmlentities() is applied with ENT_QUOTES in order to avoid 
	 * XSS through single-quote injection. However, it does not prevent strings 
	 * containing javascript within single quotes on certain attributes like 'href'.
	 * Hence the strict option.
	 */
	public static function escAttr( $str, $strict = false ){
		return Attributes::escape($str, $strict);
	}
	
	/**
	 * Returns an attribute name/value pair as a string.
	 * 
	 * @param string $attr The attribute name.
	 * @param string|array $value The attr value. If array, it is delimited by whitespace.
	 * @return string The attribute string with a leading space.
	 */
	public static function attrStr( $attr, $value ){
		return Attributes::string($attr, $value);
	}
	
	/**
	 * Returns multiple attribute name/value pairs as a single string.
	 * 
	 * @param array $attributes Assoc. array of name/value pairs.
	 * @param array $exclude Indexed array of attr names to exclude from the returned string.
	 * @return string The attributes string with a leading space.
	 */
	public static function attrsStr( array $attributes, array $exclude = array() ){
		return Attributes::arrayString($attributes);
	}
	
	/**
	 * Returns attribute(s) as a string.
	 * 
	 * @param string|array $args The attributes as a string or assoc. array.
	 * @return string The attribute string with a leading space.
	 */
	public static function parseAttrsToStr( $args ){
		return Attributes::toString($args);
	}
		
	/**
	 * Returns an opening HTML tag, (possibly) with attributes.
	 * 
	 * @param string $tag The HTML tag (default: 'div')
	 * @param array $attributes The as an assoc. array. (optional)
	 * @return string The opening HTML tag string.
	 */
	public static function tagOpen( $tag, $attributes = array() ){
		return \Phpf\Html\Element::open($tag, $attributes);
	}
		
	/**
	 * Returns a closing HTML tag.
	 * 
	 * @param string $tag The HTML tag (default: 'div')
	 * @return string The closing HTML tag string.
	 */
	public static function tagClose( $tag ){
		return '</' . $tag . ">\n";	
	}

	/**
	 * Returns an HTML tag with given content.
	 * 
	 * @param string $tag The HTML tag (default: 'div')
	 * @param array $attributes The as an assoc. array. (optional)
	 * @param string $content The content to place inside the tag.
	 * @return string The HTML tag wrapped around the given content.
	 */
	public static function tag( $tag, $attributes = array(), $content = '' ){
		return \Phpf\Html\Element::tag($tag, $attributes, $content);
	}
	
	/**
	 * Returns a <script> tag
	 */
	public static function script( $url, $attrs = array() ){
		$attrs = !empty($attrs) ? self::parseAttrsToStr($attrs) : '';
		return '<script src="' . $url . '"' . $attrs . "></script>\n";
	}
	
	/**
	 * Returns a <link> tag
	 */
	public static function link( $url, $attrs = array() ){
		
		$default = array('rel' => 'stylesheet', 'type' => 'text/css');
		
		if ( !empty($attrs) ){
			$attrs = array_merge($default, self::parseAttrs($attrs));
		} else {
			$attrs = $default;
		}
		
		return '<link href="' . $url . '"' . self::attrsStr($attrs) . ">\n";
	}
	
	/**
	 * Returns a <a> tag
	 */
	public static function a( $content, $href, $attributes = array() ){
		return '<a href="' . $href . '"' . self::parseAttrsToStr($attributes) . '>' . $content . "</a>\n";
	}
	
	/**
	 * Returns a panel using Bootstrap 3
	 */
	public static function panel( $body, $heading = '', $panel_attributes = array(), $after_body = null ){
		
		$attrs = array_merge(array('class' => 'panel-default'), $panel_attributes);
		$attrs['class'] .= ' panel';
		
		$s = '<div' . self::attrsStr($attrs) . '>';
		
		if ( !empty($heading) )
			$s .= '<div class="panel-heading">' . $heading . '</div>';
		
		if ( !empty($body) )
			$s .= '<div class="panel-body">' . $body . '</div>';
		
		if ( !empty($after_body) )
			$s .= $after_body;
		
		$s .= '</div>';
		
		return $s;
	}
}
