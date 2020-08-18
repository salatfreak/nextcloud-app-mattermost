# Mattermost
Place this app in **nextcloud/apps/**

This app is the Nextcloud part of the integration of Mattermost into Nextcloud.
It embeds Mattermost into an iframe and automatically logs the user in.

See the [Mattermost Plugin](https://github.com/Salatfreak/mattermost-plugin-nextcloud).

This app is not yet suitable for productive use. It is untested, creates
Mattermost user tokens without ever deleting them and does not sychronize
users and groups to Mattermost.

## Building the app
The app can be built by using the provided Makefile by running:

    make

This requires the following things to be present:
* make
* which
* tar: for building the archive
* curl: used if phpunit and composer are not installed to fetch them from the web
* npm: for building and testing everything JS

## Running tests
You can use the provided Makefile to run all tests by using:

    make test

This will run the PHP unit and integration tests and if a package.json is present in the **js/** folder will execute **npm run test**

Of course you can also install [PHPUnit](http://phpunit.de/getting-started.html) and use the configurations directly:

    phpunit -c phpunit.xml

or:

    phpunit -c phpunit.integration.xml

for integration tests
