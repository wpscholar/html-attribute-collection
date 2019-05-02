<?php
/**
 * An HTML attribute collection class to make attribute handling in PHP simple.
 *
 * @package wpscholar
 */

namespace wpscholar;

/**
 * Class HtmlAttributeCollection
 */
class HtmlAttributeCollection implements \Countable, \IteratorAggregate {

	/**
	 * Internal storage for attribute data.
	 *
	 * @var array
	 */
	protected $atts = array();

	/**
	 * Create a new instance.
	 *
	 * @param array|string|self $atts A collection of HTML attributes
	 */
	public function __construct( $atts = array() ) {
		$this->populate( $atts );
	}

	/**
	 * Static method for creating a new instance.
	 *
	 * @param array|string|self $atts A collection of HTML attributes
	 *
	 * @return static
	 */
	public static function make( $atts = array() ) {
		return new static( $atts );
	}

	/**
	 * Takes a collection of HTML attributes in different forms and standardizes them into an array format.
	 *
	 * @param array|string|self $atts A collection of HTML attributes
	 *
	 * @return array
	 */
	protected function normalize( $atts ) {
		$attributes = array();
		if ( is_array( $atts ) ) {
			$attributes = $atts;
		} elseif ( is_string( $atts ) ) {
			$attributes = self::parse( $atts );
		} elseif ( is_object( $atts ) && is_a( $atts, get_class() ) ) {
			/**
			 * Instance of this class.
			 *
			 * @var self $atts
			 */
			$attributes = $atts->toArray();
		}

		return $attributes;
	}

	/**
	 * Get all of the items in the collection.
	 *
	 * @return array
	 */
	public function all() {
		return $this->atts;
	}

	/**
	 * Check if an attribute exists in the collection.
	 *
	 * @param string $name Attribute name
	 *
	 * @return bool
	 */
	public function has( $name ) {
		return array_key_exists( $name, $this->atts );
	}

	/**
	 * Check if an attribute exists in the collection and has a specific value.
	 *
	 * @param string      $name Attribute name
	 * @param string|bool $value Attribute value
	 *
	 * @return bool
	 */
	public function hasValue( $name, $value ) {
		return $this->has( $name ) ? $value === $this->get( $name ) : false;
	}

	/**
	 * Check if an attribute exists in the collection and the value contains a specific string.
	 *
	 * @param string $name Attribute name
	 * @param string $value Attribute value
	 *
	 * @return bool
	 */
	public function contains( $name, $value ) {
		$existingValue = $this->get( $name );
		if ( $existingValue && is_string( $existingValue ) ) {
			return strpos( $existingValue, $value ) !== false;
		}

		return false;
	}

	/**
	 * Get an attribute value from the collection.
	 *
	 * @param string $name Attribute name
	 * @param string $default Default value to return if the attribute doesn't exist
	 *
	 * @return string|bool|null Returns null by default if attribute doesn't exist.
	 */
	public function get( $name, $default = null ) {
		return $this->has( $name ) ? $this->atts[ $name ] : $default;
	}

	/**
	 * Get an attribute value in array format.
	 *
	 * @param string $name Attribute name
	 * @param string $delimiter The boundary string
	 *
	 * @return array
	 */
	public function getAsArray( $name, $delimiter = ' ' ) {
		$array = [];
		$value = $this->get( $name );
		if ( is_string( $value ) ) {
			$array = explode( $delimiter, $value );
		}

		return $array;
	}

	/**
	 * Set an attribute on the collection.
	 *
	 * @param string      $name Attribute name
	 * @param string|bool $value Attribute value (defaults to true for attributes that don't require a value)
	 *
	 * @return self
	 */
	public function set( $name, $value = true ) {
		$this->atts[ $name ] = is_bool( $value ) ? $value : trim( (string) $value );

		return $this;
	}

	/**
	 * Delete an attribute from the collection.
	 *
	 * @param string $name Attribute name
	 *
	 * @return $this
	 */
	public function delete( $name ) {
		unset( $this->atts[ $name ] );

		return $this;
	}

	/**
	 * Append a string to an attribute value.
	 *
	 * @param string $name Attribute name
	 * @param string $value Attribute value
	 *
	 * @return $this
	 */
	public function append( $name, $value ) {
		$existingValue = $this->get( $name );
		if ( $existingValue && is_string( $existingValue ) ) {
			$this->set( $name, $existingValue . $value );
		} else {
			$this->set( $name, $value );
		}

		return $this;
	}

	/**
	 * Prepend a string to an attribute value.
	 *
	 * @param string $name Attribute name
	 * @param string $value Attribute value
	 *
	 * @return $this
	 */
	public function prepend( $name, $value ) {
		$existingValue = $this->get( $name );
		if ( $existingValue && is_string( $existingValue ) ) {
			$this->set( $name, $value . $existingValue );
		} else {
			$this->set( $name, $value );
		}

		return $this;
	}

	/**
	 * Resets attributes on the collection.
	 *
	 * @param array|string|self $atts A collection of HTML attributes
	 *
	 * @return $this
	 */
	public function populate( $atts ) {
		$attributes = $this->normalize( $atts );
		array_map( array( $this, 'set' ), array_keys( $attributes ), array_values( $attributes ) );

		return $this;
	}

	/**
	 * Adds multiple attributes to the collection at once.
	 *
	 * @param array|string|self $atts A collection of HTML attributes
	 *
	 * @return $this
	 */
	public function merge( $atts ) {
		$attributes = $this->normalize( $atts );
		foreach ( $attributes as $name => $value ) {
			$this->set( $name, $value );
		}

		return $this;
	}

	/**
	 * Count the number of attributes
	 *
	 * @return int
	 */
	public function count() {
		return count( $this->all() );
	}

	/**
	 * Allow for array iteration through attributes
	 *
	 * @return \ArrayIterator
	 */
	public function getIterator() {
		return new \ArrayIterator( $this->toArray() );
	}

	/**
	 * Get all attributes as an array
	 *
	 * @return array
	 */
	public function toArray() {
		return $this->all();
	}

	/**
	 * Get all attributes as a string
	 *
	 * @return string
	 */
	public function toString() {
		$atts = array();
		foreach ( $this->atts as $name => $value ) {
			if ( is_bool( $value ) ) {
				if ( $value ) {
					$atts[] = $name;
				}
			} else {
				$atts[] = "{$name}=\"{$value}\"";
			}
		}

		return implode( ' ', $atts );
	}

	/**
	 * Get all attributes as a string on output
	 *
	 * @return string
	 */
	public function __toString() {
		return $this->toString();
	}

	/**
	 * Parse attribute string
	 *
	 * @param string $string A string of HTML attributes
	 *
	 * @return array
	 */
	public static function parse( $string ) {
		$atts = array();
		foreach ( explode( ' ', $string ) as $pair ) {
			list( $name, $value ) = explode( '=', $pair );
			$atts[ $name ] = trim( $value, '\'"' );
		}

		return $atts;
	}

}
