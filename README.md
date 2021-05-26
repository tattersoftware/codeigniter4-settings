# Tatter\Settings
Lightweight settings management for CodeIgniter 4

[![](https://github.com/tattersoftware/codeigniter4-settings/workflows/PHPUnit/badge.svg)](https://github.com/tattersoftware/codeigniter4-settings/actions?query=workflow%3A%22PHPUnit)
[![](https://github.com/tattersoftware/codeigniter4-settings/workflows/PHPStan/badge.svg)](https://github.com/tattersoftware/codeigniter4-settings/actions?query=workflow%3A%22PHPStan)
[![Coverage Status](https://coveralls.io/repos/github/tattersoftware/codeigniter4-settings/badge.svg?branch=develop)](https://coveralls.io/github/tattersoftware/codeigniter4-settings?branch=develop)

## Quick Start

1. Install with Composer: `> composer require tatter/settings`
2. Update the database: `> php spark migrate -all`
3. Use `spark` to create templates: `> php spark settings:add timezone user America/New_York`
4. Use the service to access user settings:
```
service('settings')->timezone = $_POST['timezone_preference'];
...
$userTimezone = service('settings')->timezone;
```

## Features

Provides ready-to-use settings management for CodeIgniter 4

## Installation

Install easily via Composer to take advantage of CodeIgniter 4's autoloading capabilities
and always be up-to-date:
* `> composer require tatter/settings`

Or, install manually by downloading the source files and adding the directory to
`app/Config/Autoload.php`.

Once the files are downloaded and included in the autoload, run any library migrations
to ensure the database is setup correctly:
* `> php spark migrate -all`

## Usage

Once the library is included all the resources are ready to go and you are ready to start
adding settings. You may import setting templates directly into the `settings` table or
add them manually with the CLI command `php spark settings:add`.

**Settings** also comes with a database seeder for some recommended default templates. Run
the seeder from the command line:
	
	php spark db:seed "Tatter\Settings\Database\Seeds\SettingsSeeder"

This will add appropriately-scoped templates and default values for the following settings:

| Name             | Description                                     | Data Type | Default Value                   | Protected |
| ---------------- | ----------------------------------------------- | --------- | ------------------------------- | --------- |
| siteVersion      | Current version of this project                 | string    | 1.0.0                           | Yes       |
| brandName        | Brand name for this project                     | string    | Brand                           | Yes       |
| brandLogo        | Brand logo for this project                     | string    | /assets/images/logo.png         | Yes       |
| orgName          | Your organization name                          | string    | Organization                    | Yes       |
| orgLogo          | Your organization logo                          | string    | /assets/images/logo.png         | Yes       |
| orgUrl           | Your organization URL                           | uri       | https://example.com             | Yes       |
| orgAddress       | Your organization address                       | string    | 4141 Postmark Dr  Anchorage, AK | Yes       |
| orgPhone         | Your organization phone                         | string    | (951) 262-3062                  | Yes       |
| currencyUnit     | Currency format for number helper               | string    | USD                             | Yes       |
| currencyScale    | Conversion rate to the fractional monetary unit | int       | 100                             | Yes       |
| databaseTimezone | Timezone for the database server(s              | string    | UTC                             | Yes       |
| serverTimezone   | Timezone for the web server(s)                  | string    | UTC                             | Yes       |
| timezone         | Timezone for the user                           | string    | America/New_York                | No        |
| theme            | Site display theme                              | int       | 1                               | No        |
| perPage          | Number of items to show per page                | int       | 10                              | No        |

*Warning: This list is subject to change between major versions.*

Note that the seeder will not overwrite existing values so it is safe to re-run at any time.
See also [src/Database/Seeds/SettingsSeeder.php](src/Database/Seeds/SettingsSeeder.php).

### Setting Scope

``Settings`` come in three modes: global, user, and dynamic.
* Global settings are the same for every user and provide project owners to set application-wide values; set `protected` to `1`
* User settings start with a template value but each user may make their own value that persists across sessions; set `protected` to `0`
* Dynamic settings have no template but can be created and returned on-the-fly; they only persists for the current session.

Examples:

| Name          | Scope   | Content          | Notes                                        | Protected? |
|-------------- | ------- | ---------------- | -------------------------------------------- | ---------- |
| latestRelease | Global  | 0.7.6            | Git-style tag of latest code release         | 1          |
| timezone      | User    | America/New_York | Local timezone to use across the application | 0          |
| perPage       | User    | 10               | Default number of items to show per page     | 0          |
| jobsSearch    | Dynamic | backend php      | User's most recent search term for jobs      | n/a        |


* When you release a new version of your software:

	$settings->latestRelease = $newVersion;

* When a user searches a list of jobs:

	$settings->jobsSearch = $_POST['searchTerm'];
	$data['jobs'] = $jobModel->paginate($settings->perPage);
e/Seeds/SettingsSeeder.php](src/Database/Seeds/SettingsSeeder.php).

### Magic Config

``Settings`` comes with a magic configuration file that allows direct access to template values. This is a convenient
way to access the library in a traditional framework fashion:

	$logo = config('Settings')->projectLogo;

Note that unlike the Service or Library values from the magic config are directly from the template default and are not
affected by user overrides:

	service('settings')->set('perPage', 20);

	echo service('settings')->perPage; // 20
	echo config('Settings')->perPage; // 10
