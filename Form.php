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
						call_user_func($callback, $this->get($values, $name));
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

	public function error($name, $message = null)
	{
		$value = $this->get($this->errors, $name);
		return $message && $value ? $message : $value;
	}

	private function get(array $array, $key, $default = null)
	{
		return isset($array[$key]) ? $array[$key] : $default;
	}

	//helper for outputting select options
	public function options($value, $options, $line_separator = "\n", $encoding = 'UTF-8')
	{
		$lines = array();
		foreach ($options as $key => $text) {
			$h_key = htmlspecialchars($key, ENT_QUOTES, $encoding);
			$h_text = htmlspecialchars($text, ENT_QUOTES, $encoding);
			$selected = ($value == $key ? ' selected="selected"' : '');
			$lines[] = "<option value=\"{$h_key}\"{$selected}>{$h_text}</option>";
		}
		return implode($line_separator, $lines);
	}
}

