<?php
namespace OCA\Mattermost\Settings;

use OCP\Settings\IIconSection;
use OCP\IURLGenerator;

class AdminSection implements IIconSection {
	/** @var IURLGenerator */
	private $urlGenerator;

	public function __construct(IURLGenerator $urlGenerator) {
		$this->urlGenerator = $urlGenerator;
	}

	public function getID() {
		return 'mattermost';
	}

	public function getName() {
		return 'Mattermost';
	}

	public function getPriority() {
		return 80;
	}

	public function getIcon() {
		return $this->urlGenerator->imagePath('mattermost', 'app-dark.svg');
	}
}
