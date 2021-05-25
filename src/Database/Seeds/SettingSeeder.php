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
				'datatype'   => 'string',
				'summary'    => 'Current version of this project',
				'content'    => '1.0.0',
				'protected'  => 1,
			],
			[
				'name'       => 'brandName',
				'datatype'   => 'string',
				'summary'    => 'Brand name for this project',
				'content'    => 'Brand',
				'protected'  => 1,
			],
			[
				'name'       => 'brandLogo',
				'datatype'   => 'string',
				'summary'    => 'Brand logo for this project',
				'content'    => '/assets/images/logo.png',
				'protected'  => 1,
			],
			[
				'name'       => 'orgName',
				'datatype'   => 'string',
				'summary'    => 'Your organization name',
				'content'    => 'Organization',
				'protected'  => 1,
			],
			[
				'name'       => 'orgLogo',
				'datatype'   => 'string',
				'summary'    => 'Your organization logo',
				'content'    => '/assets/images/logo.png',
				'protected'  => 1,
			],
			[
				'name'       => 'orgUrl',
				'datatype'   => 'uri',
				'summary'    => 'Your organization URL',
				'content'    => 'https://example.com',
				'protected'  => 1,
			],
			[
				'name'       => 'orgAddress',
				'datatype'   => 'string',
				'summary'    => 'Your organization address',
				'content'    => '4141 Postmark Dr  Anchorage, AK',
				'protected'  => 1,
			],
			[
				'name'       => 'orgPhone',
				'datatype'   => 'string',
				'summary'    => 'Your organization phone',
				'content'    => '(951) 262-3062',
				'protected'  => 1,
			],
			[
				'name'       => 'currencyUnit',
				'datatype'   => 'string',
				'summary'    => 'Currency format for number helper',
				'content'    => 'USD',
				'protected'  => 1,
			],
			[
				'name'       => 'currencyScale',
				'datatype'   => 'int',
				'summary'    => 'Conversion rate to the fractional monetary unit',
				'content'    => '100',
				'protected'  => 1,
			],
			[
				'name'       => 'databaseTimezone',
				'datatype'   => 'string',
				'summary'    => 'Timezone for the database server(s)',
				'content'    => 'America/New_York',
				'protected'  => 1,
			],
			[
				'name'       => 'serverTimezone',
				'datatype'   => 'string',
				'summary'    => 'Timezone for the web server(s)',
				'content'    => 'America/New_York',
				'protected'  => 1,
			],
			[
				'name'       => 'timezone',
				'datatype'   => 'string',
				'summary'    => 'Timezone for the user',
				'content'    => 'America/New_York',
				'protected'  => 0,
			],
			[
				'name'       => 'theme',
				'datatype'   => 'int',
				'summary'    => 'Site display theme',
				'content'    => '1',
				'protected'  => 0,
			],
			[
				'name'       => 'perPage',
				'datatype'   => 'int',
				'summary'    => 'Number of items to show per page',
				'content'    => '10',
				'protected'  => 0,
			],			
		];
		
		// Check for and create project setting templates
		foreach ($rows as $row)
		{
			if (! $setting = model(SettingModel::class)->where('name', $row['name'])->first())
			{
				// No match - add the row
				if (! model(SettingModel::class)->allowCallbacks(false)->insert($row))
				{
					throw new RuntimeException(implode('. ', model(SettingModel::class)->errors()));
				}
			}
		}
	}
}
