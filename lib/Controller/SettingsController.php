<?php
namespace OCA\Mattermost\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IConfig;
use OCP\IRequest;

class SettingsController extends Controller {
	private $config;

	public function __construct($AppName, IRequest $request, IConfig $config){
		parent::__construct($AppName, $request);
		$this->config = $config;
	}

	public function index() {
		return new TemplateResponse('mattermost', 'admin', [
			'site-url' => $this->config->getAppValue(
				$this->appName, 'mattermost-site-url'
			),
			'admin-token' => $this->config->getAppValue(
				$this->appName, 'mattermost-admin-token'
			),
			'shared-secret' => $this->config->getAppValue(
				$this->appName, 'mattermost-shared-secret'
			),
		]);
	}

	public function save($siteURL, $adminToken, $sharedSecret) {
		$this->config->setAppValue(
			$this->appName, 'mattermost-site-url', $siteURL
		);
		$this->config->setAppValue(
			$this->appName, 'mattermost-admin-token', $adminToken
		);
		$this->config->setAppValue(
			$this->appName, 'mattermost-shared-secret', $sharedSecret
		);
		return [];
	}
}
