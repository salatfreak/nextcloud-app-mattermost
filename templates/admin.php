<?php
script('mattermost', 'settings-admin');
style('mattermost', 'settings-admin');
?>

<div class="section" id="mattermost">
	<h2>Mattermost</h2>
	
	<div>
		<label>
			<span>Mattermost Site URL</span>
			<input
				id="mattermost-site-url"
				type="url"
				placeholder="E.g.: &quot;http://example.com:8065&quot;"
				value="<?php p($_['site-url']) ?>"
				maxlength="500"
			/>
		</label>
	</div>
	<div>
		<label>
			<span>Admin Token</span>
			<input
				id="mattermost-admin-token"
				type="text"
				value="<?php p($_['admin-token']) ?>"
				maxlength="500"
			/>
		</label>
	</div>
	<div>
		<label>
			<span>Shared Secret</span>
			<input
				id="mattermost-shared-secret"
				type="text"
				value="<?php p($_['shared-secret']) ?>"
				maxlength="500"
			/>
		</label>
	</div>
	<div class="mattermost-hints">
		<p class="info">
			<em>
				Generate the admin token as a personal access token of an administrator
				account in Mattermost.
			</em>
		</p>
		<p class="info">
			<em>
				Generate the shared secret in the Mattermost system console under
				&quot;PLUGINS -&gt; Nextcloud Integration&quot; after installing the
				<a href="https://github.com/Salatfreak/mattermost-plugin-nextcloud">
					Mattermost Plugin
				</a>.
			</em>
		</p>
	</div>
</div>

