<?php namespace Tatter\Settings\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Tatter\Settings\Models\SettingModel;

class SettingSeeder extends Seeder
{
	public function run()
	{
		// Define default project setting templates
		$rows = [
			[
				'name'       => 'siteVersion',
				'scope'      => 'global',
				'content'    => '1.0.0',
				'summary'    => 'Current version of this project',
				'protected'  => 1,
			],
			[
				'name'       => 'databaseTimezone',
				'scope'      => 'global',
				'content'    => 'America/New_York',
				'summary'    => 'Timezone for the database server(s)',
				'protected'  => 1,
			],
			[
				'name'       => 'serverTimezone',
				'scope'      => 'global',
				'content'    => 'America/New_York',
				'summary'    => 'Timezone for the web server(s)',
				'protected'  => 1,
			],
			[
				'name'       => 'timezone',
				'scope'      => 'user',
				'content'    => 'America/New_York',
				'summary'    => 'Timezone for the user',
				'protected'  => 1,
			],
			[
				'name'       => 'currencyUnit',
				'scope'      => 'global',
				'content'    => 'USD',
				'protected'  => 1,
				'summary'    => 'Currency format for number helper',
			],
			[
				'name'       => 'currencyScale',
				'scope'      => 'global',
				'content'    => '100',
				'protected'  => 1,
				'summary'    => 'Conversion rate to the fractional monetary unit',
			],
			[
				'name'       => 'theme',
				'scope'      => 'user',
				'content'    => '1',
				'protected'  => 0,
				'summary'    => 'Site display theme',
			],
			[
				'name'       => 'perPage',
				'scope'      => 'user',
				'content'    => '10',
				'summary'    => 'Number of items to show per page',
				'protected'  => 1,
			],
			[
				'name'       => 'brandName',
				'scope'      => 'global',
				'content'    => 'Bluesmith',
				'protected'  => 1,
				'summary'    => 'Brand name for this project',
			],
			[
				'name'       => 'brandLogo',
				'scope'      => 'global',
				'content'    => '/assets/images/logo.png',
				'protected'  => 1,
				'summary'    => 'Brand logo for this project',
			],
			[
				'name'       => 'orgName',
				'scope'      => 'global',
				'content'    => 'Organization',
				'protected'  => 1,
				'summary'    => 'Your organization name',
			],
			[
				'name'       => 'orgLogo',
				'scope'      => 'global',
				'content'    => '/assets/images/logo.png',
				'protected'  => 1,
				'summary'    => 'Your organization logo',
			],
			[
				'name'       => 'orgUrl',
				'scope'      => 'global',
				'content'    => 'https://example.com',
				'protected'  => 1,
				'summary'    => 'Your organization URL',
			],
			[
				'name'       => 'orgAddress',
				'scope'      => 'global',
				'content'    => '4141 Postmark Dr  Anchorage, AK',
				'protected'  => 1,
				'summary'    => 'Your organization address',
			],
			[
				'name'       => 'orgPhone',
				'scope'      => 'global',
				'content'    => '(951) 262-3062',
				'protected'  => 1,
				'summary'    => 'Your organization phone',
			],
			
		];
		
		// Check for and create project setting templates
		foreach ($rows as $row)
		{
			if (! $setting = model(SettingModel::class)->where('name', $row['name'])->first())
			{
				// No match - add the row
				model(SettingModel::class)->insert($row);
			}
		}
	}
}
