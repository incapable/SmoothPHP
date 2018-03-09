<?php

namespace Test\Model\Constraints;

use SmoothPHP\Framework\Flow\Requests\Request;
use SmoothPHP\Framework\Forms\Constraint;
use SmoothPHP\Framework\Forms\Form;

class EmailExistsConstraint extends Constraint {
	private $checkQuery;

	public function __construct() {
		global $kernel;
		$this->checkQuery = $kernel->getMySQL()->prepare('SELECT `id` FROM `users` WHERE `email` = %s');
	}

	public function checkConstraint(Request $request, $name, $label, $value, Form $form) {
		if ($this->checkQuery->execute($value)->hasData())
			$form->addErrorMessage('That email address already exists!');
	}
}