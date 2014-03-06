Util
====

General use classes and functions

_Dependencies:_ None

_Classes:_
 * `Arr`
 * `Autoloader`
 * `DataContainer`
 * `Html`
 * `Log`
 * `Path`
 * `Registry`
 * `Security`
 * `Str`

###`Arr`

_Methods:_
 * `dotGet( array &$array, $dotpath )` - Returns an array value given its dot-notated path. E.g. 'options.user.id' would look for a key 'id' in an array with key 'user' in an array with key 'options'.
 * `dotSet( array &$array, $dotpath, $value` - Sets an array value given its dot-notated path.
 * `parse( $args, $defaults = '')` - Merges an array of arguments with defaults arguments.
 * `filter( $list, $args = array(), $operator = 'AND', $keys_exist_only = false)` - Filters an array based on a set of key => value arguments.
 * `pluck( $list, $field)` - Plucks a value out of each object or array in an array.
 * `implodeNice( $array, $separator = ', ', $last_separator = ', and ' )` - Implode an array into a string separated by separator, except for the last element, which uses $last_separator. Good for natural-language lists (e.g. "one, two, and three").

###`Html`

_Methods:_
 * `parseAttrs( $attrStr )` - Parses an string of HTML or HTML-like attributes into an array (potentially nested).
 * `attrStr( $attr, $value )` - Returns an HTML attribute string given an attribute name and value.
 * `attrsStr( array $attributes, array $exclude = array() )` - Returns HTML attribute string of given attributes, except for those listed in $exclude.
 * `parseAttrsToStr( $args )` - Parses a string or array into a string of HTML attributes.
 * `tagOpen( $tag, $attributes = array() )` - Returns an opening HTML tag string with given attributes.
 * `tagClose( $tag )` - Returns a closing HTML tag string.
 * `tag( $tag, $attributes = array(), $content = '' )` - Returns an HTML element with given attributes and content.
 * `script( $url, array $attrs = array() )` - Returns a `<script>` tag with `src` set to $url, other attributes optional.
 * `link( $url, array $attrs = array() )` - Returns a `<link>` tag with `href` set to $url, other attributes optional.
 * `a( $content, $href, array $attrs = array() )` - Returns a `<a>` tag with `href` set to $href, other attributes optional.
