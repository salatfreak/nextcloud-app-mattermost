<?php

namespace OCA\Mattermost\Settings;

use OCP\Settings\ISettings;

use OCA\Mattermost\Controller\SettingsController;

class AdminSettings implements ISettings {
	public function getForm() {
		return \OC::$server->query(SettingsController::class)->index();
	}

	public function getSection() {
		return 'mattermost';
	}

	public function getPriority() {
		return 50;
	}
}
