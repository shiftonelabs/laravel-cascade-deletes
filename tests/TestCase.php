<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests;

use ReflectionProperty;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;
use PHPUnit\Framework\TestCase as PhpunitTestCase;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class TestCase extends PhpunitTestCase
{
    /**
     * Use the integration trait so PHPUnit understands Mockery assertions.
     */
    use MockeryPHPUnitIntegration;

    /**
     * Setup the database connection.
     *
     * @return void
     */
    public function setUpDatabaseConnection()
    {
        $db = new DB();

        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
        ]);

        $db->setEventDispatcher(new Dispatcher(new Container()));

        $db->bootEloquent();
        $db->setAsGlobal();

        // This is required for testing Model events. If this is not done, the
        // events will only fire on the first test.
        Model::clearBootedModels();
    }

    /**
     * Get a schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function schema($connection = 'default')
    {
        return $this->connection($connection)->getSchemaBuilder();
    }

    /**
     * Get a database connection instance.
     *
     * @return \Illuminate\Database\Connection
     */
    protected function connection($connection = 'default')
    {
        return Model::getConnectionResolver()->connection($connection);
    }

    /**
     * Use reflection to set the value of a restricted (private/protected)
     * property on an object.
     *
     * @param  object  $object
     * @param  string  $property
     * @param  mixed  $value
     *
     * @return void
     */
    protected function setRestrictedValue($object, $property, $value)
    {
        $reflectionProperty = new ReflectionProperty($object, $property);
        $reflectionProperty->setAccessible(true);

        if ($reflectionProperty->isStatic()) {
            $reflectionProperty->setValue($value);
        } else {
            $reflectionProperty->setValue($object, $value);
        }
    }
}
