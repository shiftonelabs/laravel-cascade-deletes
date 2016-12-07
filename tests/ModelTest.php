<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests;

use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\ExtendedUser;

class ModelTest extends TestCase
{
    public function testModelUsesCascadingDeletesTrait()
    {
        $user = new ExtendedUser();

        $this->assertContains(CascadesDeletes::class, class_uses_recursive($user));
    }
}
