<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests;

use ShiftOneLabs\LaravelCascadeDeletes\CascadesDeletes;
use ShiftOneLabs\LaravelCascadeDeletes\Tests\Models\ExtendedUser;

class ModelTest extends TestCase
{
    public function testModelUsesCascadingDeletesTrait()
    {
        $this->assertContains(CascadesDeletes::class, class_uses_recursive(ExtendedUser::class));
    }
}
