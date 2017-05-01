<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AnonymousUser.php
 * Anonymous user, not logged in
 */

namespace SmoothPHP\Framework\Authentication\UserTypes;

class AnonymousUser implements AbstractUser {

	public function isLoggedIn() {
		return false;
	}

}