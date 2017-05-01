<?php

/* !
 * SmoothPHP
 * This file is part of the SmoothPHP project.
 * * * *
 * Copyright (C) 2017 Rens Rikkerink
 * License: https://github.com/Ikkerens/SmoothPHP/blob/master/License.md
 * * * *
 * AbstractUser.php
 * Simple interface which allows login-checking
 */

namespace SmoothPHP\Framework\Authentication\UserTypes;

interface AbstractUser {

	public function isLoggedIn();

}