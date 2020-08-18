import {generateUrl} from '@nextcloud/router'
import {showSuccess, showError} from '@nextcloud/dialogs'

$(function() {
	// Post settings to server on change
	$("#mattermost input").change(function() {
		$.post(generateUrl('/apps/mattermost/settings'), {
			siteURL: $("#mattermost-site-url").val(),
			adminToken: $("#mattermost-admin-token").val(),
			sharedSecret: $("#mattermost-shared-secret").val(),
		}).done(function() {
			showSuccess("Mattermost settings saved");
		}).fail(function() {
			showError("Saving Mattermost settings failed");
		});
	});
});
