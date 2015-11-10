<?php

/* https://github.com/jordanlev/php-form-helper */

class FormHelper
{
	public $fields = array();
	public $values = array();
	public $errors = array();

	public $form_error_separator = '<br>'; //inserted between individual error messages when `echo $form->errors` is called
	public $field_error_separator = ', '; //inserted between individual error messages when `echo $form->error('field')` is called

	private $validation_callback;

	public function __construct(array $values, array $fields, $validation_callback, $form_error_separator = null, $field_error_separator = null)
	{
		if (!is_null($form_error_separator)) {
			$this->form_error_separator = $form_error_separator;
		}
		if (!is_null($field_error_separator)) {
			$this->field_error_separator = $field_error_separator;
		}

		$this->fields = $fields;
		$this->values = array_intersect_key($values, array_flip($fields));
		$this->validation_callback = $validation_callback;
	}

	public function validate()
	{
		$error = new FormErrorsCollection($this->form_error_separator, $this->field_error_separator);
		call_user_func($this->validation_callback, $this, $error);

		$this->errors = count($error) ? $error : array(); //set to empty array if no errors so `if ($form->errors)` works as expected
		return !$this->errors;
	}

	public function __get($name)
	{
		return isset($this->values[$name]) ? $this->values[$name] : null;
	}

	public function __isset($name)
	{
		return isset($this->values[$name]);
	}

	//If there is an error for the given field,
	// returns the given $message (if provided),
	// or the error message (if no $message is provided).
	//Returns null if the field has no errors.
	public function error($field, $message = null)
	{
		$error = $this->errors ? $this->errors->get($field) : null;
		return $message && $error ? $message : $error;
	}

	//Returns the list of errors as an array (instead of the custom error objects).
	//You probably shouldn't need to ever use this, but it's here if you do.
	//If a $field is provided, we return an array of error messages for that field.
	//Otherwise, we return an array of all error messages.
	public function errors($field = null)
	{
		$messages = array();

		$errors = is_null($field) ? $this->errors : $this->error($field);

		if ($errors) {
			foreach ($errors as $error) {
				$messages[] = $error;
			}
		}

		return $messages;
	}

}

//Allows errors to be iterated over OR accessed on a per-field basis.
//If you "foreach" over this class, it treats all fields' errors as a single flat list.
//If you call the ->get() method, you can retrieve errors for just the designated field.
class FormErrorsCollection implements Iterator, Countable
{
	private $errors_flat = array(); //array of all error messages, regardless of field
	private $errors_grouped = array(); //array of FormFieldErrors objects, one each per field

	private $form_error_separator;
	private $field_error_separator;

	public function __construct($form_error_separator, $field_error_separator)
	{
		$this->form_error_separator = $form_error_separator;
		$this->field_error_separator = $field_error_separator;
	}

	public function __set($name, $value)
	{
		$this->errors_flat[] = $value;

		if (!array_key_exists($name, $this->errors_grouped)) {
			$this->errors_grouped[$name] = new FormFieldErrors($this->field_error_separator);
		}
		$this->errors_grouped[$name]->add($value);
	}

	public function __toString() {
		return implode($this->form_error_separator, $this->errors_flat);
	}

	public function get($field)
	{
		return array_key_exists($field, $this->errors_grouped) ? $this->errors_grouped[$field] : null;
	}

	/*** Iterator Implementation ***/
	function rewind() { reset($this->errors_flat); }
	function current() { return current($this->errors_flat); }
	function key() { return key($this->errors_flat); }
	function next() { next($this->errors_flat); }
	function valid() { return (key($this->errors_flat) !== null); }

	/*** Countable Implementation ***/
	function count() { return count($this->errors_flat); }
}

//Basically an array, but we wanted the __toString functionality
// (so you can just echo a form's error if you know it can only have 1).
class FormFieldErrors implements Iterator, Countable {
	private $errors = array();
	private $separator;

	public function __construct($separator)
	{
		$this->separator = $separator;
	}

	public function __toString()
	{
		return implode($this->separator, $this->errors);
	}

	public function add($error)
	{
		$this->errors[] = $error;
	}

	/*** Iterator Implementation ***/
	function rewind() { reset($this->errors); }
	function current() { return current($this->errors); }
	function key() { return key($this->errors); }
	function next() { next($this->errors); }
	function valid() { return (key($this->errors) !== null); }

	/*** Countable Implementation ***/
	function count() { return count($this->errors); }
}