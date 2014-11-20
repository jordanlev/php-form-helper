<?php
require 'Form.php';

//helper function for markup (unrelated to the Form class... just to make sample markup cleaner)
function h($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

// Defining the form using a class because it's actually cool to name forms.
// But we could have also pass the fields to the constructor.
class RegistrationForm extends Form {
	public function init() {
		$this->fields = array(
			'email' =>
				function($value) {
					Form::check(!empty($value), 'Email is required');
					Form::check(filter_var($value, FILTER_VALIDATE_EMAIL), 'Invalid email');
				},
			'password' =>
				function($value) {
					Form::check(strlen($value) >= 6, 'Password must be at least 6 characters');
				},
			'password_confirmation' =>
				function($value, $form) { //accept 2nd arg so other fields can be referenced within the closure. Note that you could instead reference `$this` inside the closure (or in php 5.3, assign `$form = $this` at the top of the init() function and then put `use ($variable)` on the anonymous function), but that would only work if you are defining fields in the `init()` method (not passing them into the constructor).
					Form::check($form->password == $value, 'Password confirmation must match');
				},
			'picture' =>
				function($value) {
					//NOTE: When a check fails, the remaining checks are NOT run.
					// This means you will only ever get 1 error message per field.
					// (Actually, even if all checks were run, you'd still only wind up
					// with 1 error message [the last one] because the Form class
					// does not store multiple errors per field.)
					Form::check($value['error'] != UPLOAD_ERR_NO_FILE, 'Picture is required');
					Form::check($value['size'] < 307200, 'Please upload a picture less than 300k');
					Form::check(in_array($value['type'], array('image/gif', 'image/jpeg', 'image/png')),
						'Uploaded picture must be a gif, jpeg or png');
				},
			'topic' =>
				function($value) {
					Form::check(!empty($value), 'Topic is required');
				},
			'comments' => null, //no validation is needed on this field
			'agree_to_terms' =>
				function($value) {
					Form::check(!empty($value), 'You must check the "Agree to Terms" box');
				},
			'misc_choices' => null, //there will be several checkboxes with name="misc_choices[]", to demonstrate how to work with multiple-selection lists (they are tricky!)
		);
	}

}


// Do this in your controller or something

$form = new RegistrationForm($_POST + $_FILES);
?>


<?php if ($form->errors): /* re-display form with validation errors... */ ?>
	<ul class="errors">
		<?php foreach ($form->errors as $key => $message): ?>
			<li><?= h($message) ?></li>
		<?php endforeach ?>
	</ul>
<?php elseif ($form->values): /* save form data and display success message, redirect, etc... */ ?>
	<div>
		Email: <?= h($form->email) ?><br>
		Password: <?= h($form->password) ?><br>
		Picture: <?= h($form->picture['name']) ?><br>
		Topic: <?= h($form->topic) ?><br>
		Comments: <?= nl2br(h($form->comments)) ?><br>
		Misc. Choices: <?= h(implode(', ', array_keys($form->misc_choices))) ?><br>
	</div>
<?php endif ?>

<style>.error { color: red; }</style>

<form action="" method="post" enctype="multipart/form-data">

	<div class="<?= $form->error('email', 'error') ?>">
		<label for="email">Email</label>
		<div>
			<input type="text" id="email" name="email" value="<?= h($form->email) ?>">
		</div>
		<?php if ($form->error('email')): ?>
			<div class="error">
				<?= h($form->error('email')) ?>
			</div>
		<?php endif ?>
	</div>

	<div class="<?= $form->error('password', 'error') ?>">
		<label for="password">Password</label>
		<div>
			<input type="password" id="password" name="password" value="<?= h($form->password) ?>">
		</div>
		<?php if ($form->error('password')): ?>
			<div class="error">
				<?= h($form->error('password')) ?>
			</div>
		<?php endif ?>
	</div>

	<div class="<?= $form->error('password_confirmation', 'error') ?>">
		<label for="password_confirmation">Confirm</label>
		<div>
			<input type="password" id="password_confirmation" name="password_confirmation" value="<?= h($form->password_confirmation) ?>">
		</div>
		<?php if ($form->error('password_confirmation')): ?>
			<div class="error">
				<?= h($form->error('password_confirmation')) ?>
			</div>
		<?php endif ?>
	</div>

	<div class="<?= $form->error('picture', 'error') ?>">
		<label for="picture">Picture</label>
		<div>
			<input type="file" id="picture" name="picture">
		</div>
		<?php if ($form->error('picture')): ?>
			<div class="error">
				<?= h($form->error('picture')) ?>
			</div>
		<?php endif ?>
	</div>

	<div class="<?= $form->error('topic', 'error') ?>">
		<label for="topic">Topic</label>
		<div>
			<select id="topic" name="topic">
				<?= $form->options('topic', array(
					'' => '--Choose One--',
					'first' => 'First Topic',
					'second' => 'Sectond Topic',
					'third' => 'Yet Another One',
				)) ?>
			</select>
		</div>
		<?php if ($form->error('topic')): ?>
			<div class="error">
				<?= h($form->error('topic')) ?>
			</div>
		<?php endif ?>
	</div>
	
	<div>
		<label for="comments">Comments (optional):</label>
		<textarea id="comments" name="comments"><?= h($form->comments) ?></textarea>
	</div>
	
	<div class="<?= $form->error('agree_to_terms', 'error') ?>">
		<div>
			<label>
				<input type="checkbox" id="agree_to_terms" name="agree_to_terms" value="1" <?= $form->agree_to_terms ? 'checked' : '' ?>>
				I agree to the terms &amp; conditions
			</label>
		</div>
		<?php if ($form->error('agree_to_terms')): ?>
			<div class="error">
				<?= h($form->error('agree_to_terms')) ?>
			</div>
		<?php endif ?>
	</div>

	<div>
		<label>
			<input type="checkbox" name="misc_choices[7]" value="1" <?= empty($form->misc_choices[7]) ? '' : 'checked' ?>>
			Test Item #7
		</label>
		<br>

		<label>
			<input type="checkbox" name="misc_choices[9]" value="1" <?= empty($form->misc_choices[9]) ? '' : 'checked' ?>>
			Test Item #9
		</label>
		<br>

		<label>
			<input type="checkbox" name="misc_choices[15]" value="1" <?= empty($form->misc_choices[15]) ? '' : 'checked' ?>>
			Test Item #15
		</label>
		<br>

		<label>
			<input type="checkbox" name="misc_choices[pizza]" value="1" <?= empty($form->misc_choices['pizza']) ? '' : 'checked' ?>>
			I like pizza!
		</label>
		<br>
	</div>
	
	<div>
		<input type="submit" value="Submit">
	</div>
</form>
