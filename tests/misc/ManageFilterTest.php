<?php

use App\Filters\ManageFilter;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FilterTestTrait;
use Myth\Auth\Exceptions\PermissionException;
use Tests\Support\AuthenticationTrait;
use Tests\Support\ProjectTestCase;

/**
 * @internal
 */
final class ManageFilterTest extends ProjectTestCase
{
    use AuthenticationTrait;
    use DatabaseTestTrait;
    use FilterTestTrait;

    public function testNotAuthenticated()
    {
        $this->resetAuthServices();

        $caller = $this->getFilterCaller(ManageFilter::class, 'before');
        $result = $caller();

        $this->assertInstanceOf(RedirectResponse::class, $result);
        $this->assertSame(site_url('login'), $result->getHeaderLine('Location'));
    }

    public function testNotAuthorized()
    {
        $this->expectException(PermissionException::class);
        $this->expectExceptionMessage(lang('Auth.notEnoughPrivilege'));

        $caller = $this->getFilterCaller(ManageFilter::class, 'before');
        $caller();
    }

    public function testValid()
    {
        $this->addPermissionToUser('manageAny');

        $caller = $this->getFilterCaller(ManageFilter::class, 'before');
        $result = $caller();

        $this->assertNull($result);
    }
}
