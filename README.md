# Tatter\Settings
Lightweight settings management for CodeIgniter 4

## Quick Start

1. Install with Composer: `> composer require tatter/settings`
2. Update the database: `> php spark migrate -all`
3. Add settings: `> php spark settings:add timezone user America/New_York`
4. Load the service: `$settings = service('settings');`
5. Get/set settings per user:
```
$settings->timezone = $_POST['timezone_preference'];
...
$userTimezone = $settings->timezone;
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

**Pro Tip:** You can add the spark command to your composer.json to ensure your database is
always current with the latest release:
```
{
	...
    "scripts": {
        "post-update-cmd": [
            "@composer dump-autoload",
            "php spark migrate -all"
        ]
    },
	...
```

## Configuration (optional)

The library's default behavior can be altered by extending its config file. Copy
**Settings.php** to **app/Config/** and follow the instructions in the
comments. If no config file is found in app/Config the library will use its own.

## Usage

Once the library is included all the resources are ready to go and you are ready to start
adding settings. You may import setting templates directly into the `settings` table or
add them manually with the CLI command `php spark settings:add`.

### Scope

Settings take one of three scopes: session, user, global.
* Global settings are the same for every user and provide a dynamic way to set application-wide values
* User settings start with a default template value but each user may make their own default that persists across sessions
* Session settings start with a default template value and may be changed by a user but revert for each new session

Examples:
```
+---------------+---------+------------------+----------------------------------------------+------------+
| Name          | Scope   | Content          | Notes                                        | Protected? |
+---------------+---------+------------------+----------------------------------------------+------------+
| latestRelease | global  | 0.7.6            | Git-style tag of latest code release         | 0          |
| perPage       | user    | 10               | Default number of items to show per page     | 1          |
| timezone      | user    | America/New_York | Local timezone to use across the application | 1          |
| jobsSearch    | session |                  | User's most recent search term for jobs      | 1          |
+---------------+---------+------------------+----------------------------------------------+------------+
```

* When you release a new version of your software:

`$settings->latestRelease = $newVersion;`

* When a user searches a list of jobs:

```
$settings->jobsSearch = $_POST['searchTerm'];
$data['jobs'] = $jobModel->paginate($settings->perPage);
```
