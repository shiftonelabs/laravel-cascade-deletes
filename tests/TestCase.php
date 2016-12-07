<?php

namespace ShiftOneLabs\LaravelCascadeDeletes\Tests;

use ReflectionMethod;
use ReflectionProperty;
use PHPUnit_Framework_TestCase;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class TestCase extends PHPUnit_Framework_TestCase
{
    /**
     * Setup the database connection.
     *
     * @return void
     */
    public function setUpDatabaseConnection()
    {
        $db = new DB;

        $db->addConnection([
            'driver'    => 'sqlite',
            'database'  => ':memory:',
        ]);

        $db->setEventDispatcher(new Dispatcher(new Container()));

        $db->bootEloquent();
        $db->setAsGlobal();

        // This is required for testing Model events. If this is not done, the
        // events will only fire on the first test. clearBootedModels() was
        // added in 5.1, so use it if possible, otherwise use reflection.
        if (method_exists(Model::class, 'clearBootedModels')) {
            Model::clearBootedModels();
        } else {
            $this->setRestrictedValue(Model::class, 'booted', []);
            // Laravel 4.1 does not have the globalScopes property
            if (property_exists(Model::class, 'globalScopes')) {
                $this->setRestrictedValue(Model::class, 'globalScopes', []);
            }
        }
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
     * Get a schema builder instance.
     *
     * @return \Illuminate\Database\Schema\Builder
     */
    protected function schema($connection = 'default')
    {
        return $this->connection($connection)->getSchemaBuilder();
    }

    /**
     * Use reflection to call a restricted (private/protected) method on an object.
     *
     * @param  object  $object
     * @param  string  $method
     * @param  array   $args
     *
     * @return mixed
     */
    protected function callRestrictedMethod($object, $method, array $args = [])
    {
        $reflectionMethod = new ReflectionMethod($object, $method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($object, $args);
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
