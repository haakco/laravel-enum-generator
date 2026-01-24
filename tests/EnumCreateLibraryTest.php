<?php

declare(strict_types=1);

namespace HaakCo\LaravelEnumGenerator\Tests;

use HaakCo\LaravelEnumGenerator\Libraries\System\EnumCreateLibrary;
use Orchestra\Testbench\TestCase;
use ReflectionClass;
use RuntimeException;

class EnumCreateLibraryTest extends TestCase
{
    public function testItBuildsWhereClausesWithSingleCondition(): void
    {
        $library = new EnumCreateLibrary();
        $result = $this->invokePrivateMethod($library, 'buildWhereClauses', [
            [
                'is_active' => true,
            ],
        ]);

        [$clauses, $bindings] = $result;

        $this->assertCount(1, $clauses);
        $this->assertEquals('is_active = ?', $clauses[0]);
        $this->assertCount(1, $bindings);
        $this->assertTrue($bindings[0]);
    }

    public function testItBuildsWhereClausesWithMultipleConditions(): void
    {
        $library = new EnumCreateLibrary();
        $result = $this->invokePrivateMethod($library, 'buildWhereClauses', [
            [
                'is_active' => true,
                'type' => 'public',
                'status' => 1,
            ],
        ]);

        [$clauses, $bindings] = $result;

        $this->assertCount(3, $clauses);
        $this->assertEquals('is_active = ?', $clauses[0]);
        $this->assertEquals('type = ?', $clauses[1]);
        $this->assertEquals('status = ?', $clauses[2]);

        $this->assertCount(3, $bindings);
        $this->assertTrue($bindings[0]);
        $this->assertEquals('public', $bindings[1]);
        $this->assertEquals(1, $bindings[2]);
    }

    public function testItBuildsWhereClausesWithNullValue(): void
    {
        $library = new EnumCreateLibrary();
        $result = $this->invokePrivateMethod($library, 'buildWhereClauses', [
            [
                'deleted_at' => null,
            ],
        ]);

        [$clauses, $bindings] = $result;

        $this->assertCount(1, $clauses);
        $this->assertEquals('deleted_at = ?', $clauses[0]);
        $this->assertCount(1, $bindings);
        $this->assertNull($bindings[0]);
    }

    public function testItReturnsEmptyArraysForEmptyWhere(): void
    {
        $library = new EnumCreateLibrary();
        $result = $this->invokePrivateMethod($library, 'buildWhereClauses', [[]]);

        [$clauses, $bindings] = $result;

        $this->assertCount(0, $clauses);
        $this->assertCount(0, $bindings);
    }

    public function testItRejectsInvalidColumnNames(): void
    {
        $library = new EnumCreateLibrary();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invalid where column');

        $this->invokePrivateMethod($library, 'buildWhereClauses', [
            [
                'column; DROP TABLE users;--' => 'malicious',
            ],
        ]);
    }

    public function testItAllowsDottedColumnNames(): void
    {
        $library = new EnumCreateLibrary();
        $result = $this->invokePrivateMethod($library, 'buildWhereClauses', [
            [
                'table.column' => 'value',
            ],
        ]);

        [$clauses, $bindings] = $result;

        $this->assertCount(1, $clauses);
        $this->assertEquals('table.column = ?', $clauses[0]);
    }

    protected function getPackageProviders($app): array
    {
        return [\HaakCo\LaravelEnumGenerator\LaravelEnumGeneratorServiceProvider::class];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('enum-generator.tables', []);
        $app['config']->set('enum-generator.enumPath', sys_get_temp_dir() . '/enums');
    }

    private function invokePrivateMethod(object $object, string $methodName, array $parameters = []): mixed
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
