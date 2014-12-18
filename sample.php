<?php

require 'Form.php';

//set up the form...

	$fields = array(
		'email',
		'password',
		'password_confirmation',
		'picture',
		'topic',
		'comments',
		'agree_to_terms',
		'misc_choices', //there will be several checkboxes with name="misc_choices[]", to demonstrate how to work with multiple-selection lists (they are tricky!)
	);

	$validation = function ($form, $error) {
		if (empty($form->email)) {
			$error->email = 'Email is required';
		} else if (!filter_var($form->email, FILTER_VALIDATE_EMAIL)) {
			$error->email = 'Invalid email';
		}

		if (empty($form->password)) {
			$error->password = 'Password is required';
		} else {
			if (strlen($form->password) < 6) {
				$error->password = 'Password must be at least 6 characters';
			}
			if (!preg_match('/[0-9]+/', $form->password)) {
				$error->password = 'Password must contain at least 1 digit';
			}
			if ($form->password != $form->password_confirmation) {
				$error->password_confirmation = 'Password confirmation must match';
			}
		}

		if ($form->picture['error'] == UPLOAD_ERR_NO_FILE) {
			$error->picture = 'Picture is required';
		} else {
			if ($form->picture['size'] > 307200) {
				$error->picture = 'Please upload a picture less than 300k';
			}
			if (!in_array($form->picture['type'], array('image/gif', 'image/jpeg', 'image/png'))) {
				$error->picture = 'Uploaded picture must be a gif, jpeg or png';
			}
		}

		if (empty($form->topic)) {
			$error->topic = 'Topic is required';
		}

		if (empty($form->agree_to_terms)) {
			$error->agree_to_terms = 'You must check the "Agree to Terms" box';
		}

		if (empty($form->misc_choices)) {
			$form->misc_choices = array();
		}
	};
	
	if ($_POST || $_FILES) {
		$data = $_POST + $_FILES;
	} else {
		$data = array('comments' => 'Testing 1 2 3' /* ... you could pre-populate the form with defaults or database data here if you like */ );
	}

	$form = new Form($data, $fields, $validation);



//validate and process the form...
	if ($_POST && $form->validate()) {
		//save to database, send off an email, redirect, whatever...
		die('Thank you for submitting the following information:<br><br>'
			. 'Email: ' . h($form->email) . '<br>'
			. 'Password: ' . h($form->password) . '<br>'
			. 'Picture: ' . h($form->picture['name']) . '<br>'
			. 'Topic: ' . h($form->topic) . '<br>'
			. 'Comments: ' . nl2br(h($form->comments)) . '<br>'
			. 'Misc. Choices: ' . h(implode(', ', array_keys($form->misc_choices))) . '<br>'
		);
	}


//display the form...
?>

	<?php if ($form->errors): /* re-display form with validation errors... */ ?>
		<ul class="errors">
			<?php foreach ($form->errors as $error): ?>
				<li><?= $error ?></li>
			<?php endforeach; ?>
		</ul>
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
					<?= $form->error('email') ?>
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
					<?= $form->error('password') ?>
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
					<?= $form->error('password_confirmation') ?>
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
					<?= $form->error('picture') ?>
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
					<?= $form->error('topic') ?>
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
					<?= $form->error('agree_to_terms') ?>
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


<?php
//helper function for markup (unrelated to the Form class... just makes the sample markup cleaner)
function h($s) {
	return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
?>