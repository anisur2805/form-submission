<?php

namespace AFS\Form_Submission;

use AFS\Form_Submission\Admin\Menu;
use AFS\Form_Submission\Admin\Role;
use AFS\Form_Submission\Admin\Widgets\Form_Widget;

class Admin {
	public function __construct() {
		new Menu();

		new Role();

		new Form_Widget();
	}
}
