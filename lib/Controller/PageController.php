<?php
namespace OCA\Mattermost\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

use OCA\Mattermost\Service\InvalidConfigException;
use OCA\Mattermost\Service\MMAuthException;
use OCA\Mattermost\Service\MMRequestException;
use OCA\Mattermost\Service\MattermostService;
use OCA\Mattermost\Service\NotConfiguredException;

class PageController extends Controller {
	private $userId;
	private $mattermostService;

	public function __construct(
		$AppName, IRequest $request, MattermostService $mattermostService, $UserId
	){
		parent::__construct($AppName, $request);
		$this->userId = $UserId;
		$this->mattermostService = $mattermostService;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index() {
		// Create login token
		try {
			list($scheme, $host, $path) = $this->mattermostService->getSiteURL();
			$loginToken = $this->mattermostService->createLoginToken(
				$scheme, $host, $path, $this->userId
			);
		} catch (NotConfiguredException $e) {
			return new TemplateResponse('mattermost', 'error', [
				'message' => 'The Mattermost App is not properly configured yet.',
			]);
		} catch (InvalidConfigException $e) {
			return new TemplateResponse('mattermost', 'error', [
				'message' => 'The Configuration of the Mattermost App is faulty.' . 
				$e->getMessage(),
			]);
		} catch (MMAuthException $e) {
			return new TemplateResponse('mattermost', 'error', [
				'message' => 'Authenticating at the Mattermost failed.',
			]);
		} catch (MMRequestException $e) {
			return new TemplateResponse('mattermost', 'error', [
				'message' => 'Request to the Mattermost server failed',
			]);
		}
		
		// Assemble login URL
		$loginURL = "${scheme}://${host}${path}" . 
			'/plugins/com.github.salatfreak.mattermost-plugin-nextcloud/login/' .
			$loginToken;

		// Render template with login URL
		$response = new TemplateResponse(
			'mattermost', 'index', ['url' => $loginURL]
		);

		// Add security policy
		$policy = new ContentSecurityPolicy();
		$policy->addAllowedChildSrcDomain("${scheme}://${host}");
		$policy->addAllowedFrameDomain("${scheme}://${host}");
		$response->setContentSecurityPolicy($policy);

		// Return template response
		return $response;
	}
}
