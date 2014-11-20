<?php

/* https://github.com/jordanlev/php-form-helper */

class Form
{
	public $values = array();
	public $errors = array();
	public $fields = array();

	public function __construct(array $values, $fields = null)
	{
		$this->init();
		if ($fields) $this->fields = $fields;
		$this->values = array_intersect_key($values, $this->fields);

		if ($values) {
			foreach ($this->fields as $name => $callback) {
				if (is_callable($callback)) {
					try {
						call_user_func($callback, $this->get($values, $name), $this);
					} catch (DomainException $e) {
						$this->errors[$name] = $e->getMessage();
					}
				}
			}
		}
	}

	protected function init()
	{
		// initialize $fields when subclassing
	}

	public static function check($expression, $message)
	{
		if (false == $expression) {
			throw new DomainException($message);
		}
	}

	public function __get($name)
	{
		return $this->get($this->values, $name);
	}

	//If there is an error for the given field,
	// returns the given $message (if provided),
	// or the error message (if no $message is provided).
	//Returns null if the field has no errors.
	public function error($name, $message = null)
	{
		$value = $this->get($this->errors, $name);
		return $message && $value ? $message : $value;
	}

	private function get(array $array, $key, $default = null)
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}

	//Helper for outputting select options.
	// $name is a field name whose value we will examine to determine which option is selected.
	// $options is an array of value => label pairs
	// $line_separator is just for the html output
	public function options($name, $options, $line_separator = "\n")
	{
		$lines = array();
		foreach ($options as $key => $text) {
			$value = empty($name) ? null : $this->$name;
			$selected = ($value === $key ? ' selected="selected"' : '');
			$lines[] = "<option value=\"{$key}\"{$selected}>{$text}</option>";
		}
		return implode($line_separator, $lines);
	}
}
