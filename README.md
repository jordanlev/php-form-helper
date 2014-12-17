# php-form-helper

A PHP 5.3 Form helper class that stays out of your markup.

It's late, I can't sleep, and I'm sick of those
uselessly complicated form classes out there.

## Features

- No html elements rendering
- No validators
- No form decorators
- No widgets
- No extensions
- No documentation (just look at the `sample.php` file)

## Jordan Lev's modifications in this fork:
- Fields can have more than 1 error at a time
- Fields are declared separately from validations (because there isn't always a 1:1 correlation)
- All validations are handled within a single function (to allow for more flexibility with the logic)
- Side benefit of the architectural change is that you can have a data model provide the field list and validation function, without it needing to know as much about how the Form class works (all it needs to know is that `$form->field` gives you a value and `$error->field` adds an error)
- Validation is handled explicitly by calling the validate() method on the form object (instead of always occurring during instantiation), so you can pre-populate a form with database data and not have it display errors
- Removed ability to easily subclass... you should just always pass everything into the constructor now
- Removed use of Exceptions because it prevented more than 1 error per field (and arguably is conceptually wrong -- see http://martinfowler.com/articles/replaceThrowWithNotification.html)
- Removed tests and composer files (because I didn't use them)
- Added a helper function for outputting `select` options (which is conceptually unrelated to the other class functionality, but it is so darned useful I couldn't resist [also, unlike every other form element, there is *never* a need to customize the markup of an `<option>` tag so it is the one exception to the "stay out of my markup" rule])
