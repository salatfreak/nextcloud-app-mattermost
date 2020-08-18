<?php
namespace OCA\Mattermost\Service;

use OCP\IConfig;

use OCA\Mattermost\Service\InvalidConfigException;
use OCA\Mattermost\Service\MMAuthException;
use OCA\Mattermost\Service\MMRequestException;
use OCA\Mattermost\Service\NotConfiguredException;

use Gnello\Mattermost\Driver;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;

class MattermostService {
	private $config;
	private $appName;

	public function __construct(IConfig $config, $appName) {
		$this->config = $config;
		$this->appName = $appName;
	}

	// Get site url
	public function getSiteURL() : array {
		// Get URL from config
		$siteURL = $this->config->getAppValue(
			$this->appName, 'mattermost-site-url'
		);
		if ($siteURL === '') { throw new NotConfiguredException(); }

		// Parse url
		$urlParts = parse_url($siteURL);

		// Require scheme and host to exist but not user, pass, query or fragment
		if ($urlParts === FALSE) {
			throw new InvalidConfigException("Invalid Site URL");
		}
		if (!array_key_exists('scheme', $urlParts)) {
			throw new InvalidConfigException("Site URL scheme missing");
		}
		if (!array_key_exists('host', $urlParts)) {
			throw new InvalidConfigException('Site URL host missing');
		}
		if (array_key_exists('user', $urlParts)) {
			throw new InvalidConfigException("Site URL can't have a user component");
		}
		if (array_key_exists('pass', $urlParts)) {
			throw new InvalidConfigException("Site URL can't have a password component");
		}
		if (array_key_exists('query', $urlParts)) {
			throw new InvalidConfigException("Site URL can't have a query component");
		}
		if (array_key_exists('fragment', $urlParts)) {
			throw new InvalidConfigException("Site URL can't have a fragment component");
		}

		// Assemble site host
		$host = $urlParts['host'];
		if (array_key_exists('port', $urlParts)) {
			$host .= ":${urlParts['port']}";
		}

		// Return url
		return [$urlParts['scheme'], $host, rtrim($urlParts['path'], '/')];
	}

	public function createLoginToken(
		string $scheme, string $host, string $path, string $userId
	) : string {
		// Get admin token and shared secret
		$adminToken = $this->config->getAppValue(
			$this->appName, 'mattermost-admin-token'
		);
		$sharedSecret = $this->config->getAppValue(
			$this->appName, 'mattermost-shared-secret'
		);

		// Check existence
		if ($adminToken === '' || $sharedSecret === '') {
			throw new NotConfiguredException();
		}

		// Create Mattermost API driver
		$container = new \Pimple\Container([
			'driver' => [
				'scheme' => $scheme,
				'url' => $host . $path,
				'token' => $adminToken,
			]
		]);
		$driver = new Driver($container);

		// Authenticate user
		$resp = $driver->authenticate();
		if ($resp->getStatusCode() != 200) {
			throw new MMAuthException();
		}

		// Get Mattermost user id
		$resp = $driver->getUserModel()->getUserByUsername($userId);
		if ($resp->getStatusCode() != 200) {
			throw new MMRequestException("User doesn't exist");
		}
		$mmId = json_decode($resp->getBody())->id;

		// Create personal access token
		$resp = $driver->getUserModel()->createToken($mmId, [
			"description" => "Nextcloud login"
		]);
		if ($resp->getStatusCode() != 200) {
			throw new MMRequestException('Creating user access token failed');
		}
		$authToken = json_decode($resp->getBody())->token;

		// Create login token
		$guzzle = new GuzzleClient([]);
		$target = 
			"${scheme}://${host}${path}" .
			'/plugins/com.github.salatfreak.mattermost-plugin-nextcloud/login';
		$opts = [
			RequestOptions::JSON => [
				"secret" => $sharedSecret,
				"auth_token" => $authToken,
				"user_id" => $mmId,
			]
		];
		try {
			$resp = $guzzle->{'PUT'}($target, $opts);
		} catch (RequestException $e) {
			throw new MMRequestException('Requesting login token failed');
		}
		if ($resp->getStatusCode() != 200) {
			throw new MMRequestException('Requesting login token failed');
		}
		return json_decode($resp->getBody())->token;
	}
}
