<?php

declare(strict_types=1);

namespace HaakCo\LaravelEnumGenerator\Libraries\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;

use function config;
use function is_int;
use function is_string;
use function str_replace;

class EnumCreateLibrary
{
    private Command|null $commandThis;

    private bool $defaultLeaveSchema;

    private string $defaultPrependClass;

    private string $defaultPrependName;

    private string $enumPath;

    private array $tableNames;

    private string $templateName = 'laravel-enum-generator::enums.enum-string';

    private string $templateIdName = 'laravel-enum-generator::enums.enum-id';

    private string $templateOldName = 'laravel-enum-generator::enums.enum-class';

    private string $defaultIdField;

    private string $defaultNameField;

    public function __construct()
    {
        $this->defaultLeaveSchema = config('enum-generator.default-leave-schema', true);
        $this->defaultPrependClass = config('enum-generator.default-prepend-class', '');
        $this->defaultPrependName = config('enum-generator.default-prepend_name', '');
        $this->enumPath = config('enum-generator.enumPath', app_path() . '/Models/Enums');
        $this->tableNames = config('enum-generator.tables');
        $this->defaultIdField = config('enum-generator.id-field', 'id');
        $this->defaultNameField = config('enum-generator.name-field', 'name');
    }

    public function create(?Command $commandThis = null): void
    {
        $this->commandThis = $commandThis;

        foreach ($this->tableNames as $tableName => $tableOptions) {
            try {
                if (is_string($tableOptions) && is_int($tableName)) {
                    $tableName = $tableOptions;
                    $tableOptions = [];
                }

                //Fix previous config format to be more consistent
                if (isset($tableOptions['prepend_class'])) {
                    $tableOptions['prepend-class'] = $tableOptions['prepend_class'];
                }
                if (isset($tableOptions['prepend_name'])) {
                    $tableOptions['prepend-name'] = $tableOptions['prepend_name'];
                }

                $tableOptions['leave-schema'] = $tableOptions['leave-schema'] ?? $this->defaultLeaveSchema;
                $tableOptions['prepend-class'] = $tableOptions['prepend-class'] ?? $this->defaultPrependClass;
                $tableOptions['prepend-name'] = $tableOptions['prepend-name'] ?? $this->defaultPrependName;
                $tableOptions['id-field'] = $tableOptions['id-field'] ?? $this->defaultIdField;
                $tableOptions['name-field'] = $tableOptions['name-field'] ?? $this->defaultNameField;

                $this->log('Creating enum for ' . $tableName);

                $sql = 'select ' .
                    $tableOptions['id-field'] . ' as id' .
                    ', ' . $tableOptions['name-field'] . ' as name';

                if (!empty($tableOptions['uuid'])) {
                    $sql .= ', uuid';
                }

                $sql .= ' from ' . $tableName;
                $enumDataRows = DB::select($sql);

                $className = '';
                foreach (explode('.', $tableName) as $subName) {
                    if (!empty($tableOptions['leave-schema'])) {
                        $className .= Str::studly($subName);
                    } else {
                        $className = Str::studly($subName);
                    }
                }

                $className .= 'Enum';
                if (!empty($tableOptions['prepend-class'])) {
                    $className = Str::studly($tableOptions['prepend-class']) . '_' . $className;
                }
                $className = Str::studly($className);

                foreach ($enumDataRows as $enumDataRow) {
                    $enumDataRow->nameString = strtoupper(
                        Str::slug(str_replace(['/', '+'], ['_', '_plus_'], $enumDataRow->name), '_')
                    );

                    if ($enumDataRow->nameString !== '' && is_numeric($enumDataRow->nameString[0])) {
                        $enumDataRow->nameString = 'N_' . $enumDataRow->nameString;
                    }

                    if (!empty($tableOptions['prepend-name'])) {
                        $enumDataRow->nameString = strtoupper($tableOptions['prepend_name']) .
                            '_' .
                            $enumDataRow->nameString;
                    }

                    $enumDataRow->nameString = preg_replace(
                        ['/\s+/', '/-+/'],
                        ['_', '_dash_'],
                        $enumDataRow->nameString
                    );
                }

                $nameSpace = 'App\\' . str_replace([app_path() . '/', '/'], ['', '\\'], $this->enumPath);

                if (!config('enum-generator.use-enum-format', false)) {
                    $msgHtml = "<?php\n\n" . view(
                            $this->templateOldName,
                            [
                                'nameSpace' => $nameSpace,
                                'className' => $className,
                                'tableName' => $tableName,
                                'enumDataRows' => $enumDataRows,
                                'tableOptions' => $tableOptions,
                            ]
                        )->render();

                    // if it doesn't exist create and make sure it exists
                    if (!is_dir($this->enumPath) && !mkdir($this->enumPath, 440) && !is_dir($this->enumPath)) {
                        throw new RuntimeException(sprintf('Di rectory "%s" was not created', $this->enumPath));
                    }
                    file_put_contents($this->enumPath . '/' . $className . '.php', $msgHtml);
                } else {
                    $msgHtml = "<?php\n\n" . view(
                            $this->templateName,
                            [
                                'nameSpace' => $nameSpace,
                                'className' => $className,
                                'tableName' => $tableName,
                                'enumDataRows' => $enumDataRows,
                                'tableOptions' => $tableOptions,
                            ]
                        )->render();

                    // if it doesn't exist create and make sure it exists
                    if (!is_dir($this->enumPath) && !mkdir($this->enumPath, 440) && !is_dir($this->enumPath)) {
                        throw new RuntimeException(sprintf('Di rectory "%s" was not created', $this->enumPath));
                    }
                    file_put_contents($this->enumPath . '/' . $className . '.php', $msgHtml);

                    $idClassName = $className . 'Id';
                    $msgHtml = "<?php\n\n" . view(
                            $this->templateIdName,
                            [
                                'nameSpace' => $nameSpace,
                                'className' => $idClassName,
                                'tableName' => $tableName,
                                'enumDataRows' => $enumDataRows,
                                'tableOptions' => $tableOptions,
                            ]
                        )->render();

                    // if it doesn't exist create and make sure it exists
                    if (!is_dir($this->enumPath) && !mkdir($this->enumPath, 440) && !is_dir($this->enumPath)) {
                        throw new RuntimeException(sprintf('Di rectory "%s" was not created', $this->enumPath));
                    }
                    file_put_contents($this->enumPath . '/' . $idClassName . '.php', $msgHtml);
                }
            } catch (\Exception $e) {
                $this->log('Error creating enum for ' . $tableName . ': ' . $e->getMessage());
            }
        }
    }

    private function log($msg): void
    {
        if ($this->commandThis !== null) {
            $this->commandThis->info($msg);
        }
        info($msg);
    }
}
