<?php

// Load the model
$this->load->model('user/user_group');

// Add access and modify permissions
$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'marketing/sendmail');
$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'marketing/sendmail');