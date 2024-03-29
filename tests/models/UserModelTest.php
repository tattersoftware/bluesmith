<?php

namespace App\Models;

use CodeIgniter\Test\DatabaseTestTrait;
use Myth\Auth\Test\Fakers\GroupFaker;
use Myth\Auth\Test\Fakers\UserFaker;
use Tests\Support\ProjectTestCase;

/**
 * @internal
 */
final class UserModelTest extends ProjectTestCase
{
    use DatabaseTestTrait;

    public function testConstructorMergesFields()
    {
        $model  = new UserModel();
        $result = $this->getPrivateProperty($model, 'allowedFields');

        $this->assertContains('firstname', $result);
    }

    public function testStaffIds()
    {
        $user  = fake(UserFaker::class);
        $group = model(GroupFaker::class)->where('name', 'Administrators')->first();

        model(GroupFaker::class)->addUserToGroup($user->id, $group->id);

        $result = model(UserModel::class)->findStaffIds();

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($user->id, $result[0]);
    }

    public function testGroups()
    {
        $user  = fake(UserFaker::class);
        $group = fake(GroupFaker::class);

        model(GroupFaker::class)->addUserToGroup($user->id, $group->id);

        $result = model(UserModel::class)->groups($user->id);

        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertSame($group->name, $result[0]->name);
    }

    public function testFetchCompiledRows()
    {
        fake(UserFaker::class);
        fake(UserFaker::class);

        $method = $this->getPrivateMethodInvoker(model(UserModel::class), 'fetchCompiledRows');
        $result = $method();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $keys   = array_keys($result[0]);
        $fields = [
            'id',
            'firstname',
            'lastname',
            'group_id',
            'group',
        ];

        foreach ($fields as $field) {
            $this->assertContains($field, $keys);
        }
    }
}
