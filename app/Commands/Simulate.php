<?php namespace App\Commands;

use App\Database\Seeds\InitialSeeder;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;
use RuntimeException;
use Tests\Support\Simulator;

class Simulate extends BaseCommand
{
	protected $group       = 'Database';
	protected $name        = 'simulate';
	protected $description = 'Create a simulated project in the database.';
	protected $usage       = 'simulate';

	public function run(array $params)
	{
		if (ENVIRONMENT === 'production')
		{
			throw new RuntimeException('This feature is not available on production sites.'); 
		}

		helper('test');

		// Disable foreign key locks
		$db = db_connect();
		$db->disableForeignKeyChecks();

		// Truncate the target tables
		foreach (['actions', 'jobs', 'materials', 'methods', 'stages', 'users', 'workflows'] as $table)
		{
			$db->table($table)->truncate();
		}

		// Reenable foreign key locks
		$db->enableForeignKeyChecks();

		// Run the InitialSeeder to restore defaults
		Database::seeder()->setSilent(ENVIRONMENT === 'testing')->call(InitialSeeder::class);

		// Run the Simulator
		Simulator::initialize();
	}
}
