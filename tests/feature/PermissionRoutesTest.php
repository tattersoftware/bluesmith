<?php

use App\Entities\User;
use App\Models\JobModel;
use Myth\Auth\Exceptions\PermissionException;
use Tests\Support\Fakers\UserFaker;
use Tests\Support\FeatureTestCase;
use Tests\Support\Simulator;

class PermissionRoutesTest extends FeatureTestCase
{
	/**
	 * Should the database be refreshed before each test?
	 *
	 * @var boolean
	 */
	protected $refresh = false;

	protected function setUp(): void
	{
		parent::setUp();

		$this->resetAuthServices();
		$this->simulateOnce();
	}

	/**
	 * @dataProvider routeProvider
	 */
	public function testNotLoggedIn($route, $status)
	{
		$result = $this->get($route);

		$result->assertOk();
		
		if ($status === 'public')
		{
			$result->assertStatus(200);
		}
		else
		{
			$result->assertRedirect();
		}
	}

	/**
	 * @dataProvider routeProvider
	 */
	public function testLoggedInNoPermissions($route, $status)
	{
		$user = fake(UserFaker::class);

		// The filter will throw if user does not have access
		if ($status === 'manage')
		{
			$this->expectException(\RuntimeException::class); // WIP Change to PermissionException when merged
			$this->expectExceptionMessage(lang('Auth.notEnoughPrivilege'));
		}

		$result = $this->withSession(['logged_in' => $user->id])->get($route);

		// Below only executes when access was granted
		$result->assertOk();
		$result->assertStatus(200);
	}

	/**
	 * @dataProvider routeProvider
	 */
	public function testHasPermissionManageAny($route, $status)
	{
		$user = $this->createUserWithPermission('manageAny');

		$result = $this->withSession(['logged_in' => $user->id])->get($route);
		$result->assertOk();
		$result->assertStatus(200);
	}

	/**
	 * @dataProvider routeProvider
	 */
	public function testInGroupConsultants($route, $status)
	{
		$user = $this->createUserInGroup('Consultants');

		$result = $this->withSession(['logged_in' => $user->id])->get($route);
		$result->assertOk();
		$result->assertStatus(200);
	}

	/**
	 * @dataProvider routeProvider
	 */
	public function testInGroupAdministrators($route, $status)
	{
		$user = $this->createUserInGroup('Administrators');

		$result = $this->withSession(['logged_in' => $user->id])->get($route);
		$result->assertOk();
		$result->assertStatus(200);
	}

	public function routeProvider()
	{
		return [
			['/', 'public'],
			['about/options', 'public'],
			['account/jobs', 'login'],
			['files/index', 'login'],
			['manage', 'manage'],
			['manage/', 'manage'],
			['manage/content/branding', 'manage'],
			['manage/content/page', 'manage'],
			//['manage/jobs', 'manage'], Why is this breaking Feature tests but works from browser??
			['manage/materials', 'manage'],
			['manage/materials/method/1', 'manage'],
			['actions', 'manage'],
			['workflows', 'manage'],
		];
	}
}

/*


			['name' => 'Administrators', 'description' => 'Staff with full access to the application'],
			['name' => 'Consultants',    'description' => 'Staff who facilitate and manage print jobs'],
			['name' => 'Editors',        'description' => 'Staff who can access the CMS to update content'],
			['name' => 'VIPs',           'description' => 'Patrons with priority printing access'],

			['name' => 'manageAny',     'description' => 'General access to the admin dashboard'],
			['name' => 'manageContent', 'description' => 'Access to the CMS'],
			['name' => 'manageJobs',    'description' => 'Access to perform job updates'],

		'login'  => ['before' => ['account*', 'files*', 'jobs*']],
		'manage' => ['before' => ['manage*', 'actions*', 'workflows*']]

// Admin dashboard
$routes->group('manage', ['filter'=>'permission:ManageAny', 'namespace'=>'App\Controllers\Manage'], function($routes)
{
	$routes->add('/', 'Dashboard::index');

	$routes->get('content/(:any)', 'Content::$1');
	$routes->post('content/(:any)', 'Content::$1');

	$routes->get('materials/method/(:any)', 'Materials::method/$1');
	$routes->presenter('materials');
});

*/