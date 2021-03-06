<?php

namespace HaakCo\LaravelEnumGenerator\Libraries\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

use function str_replace;

class EnumCreateLibrary
{
    private $commandThis;

    /**
     * @var boolean
     */
    private $defaultLeaveSchema;
    /**
     * @var boolean
     */
    private $defaultUseUuid;
    /**
     * @var string
     */
    private $defaultPrependClass;
    /**
     * @var string
     */
    private $defaultPrependName;
    /**
     * @var string
     */
    private $enumPath;
    /**
     * @var array
     */
    private $tableNames;

    public function __construct()
    {
        $this->defaultLeaveSchema = config('enum-generator.default-leave-schema', true);
        $this->defaultUseUuid = config('enum-generator.default-uuid', false);
        $this->defaultPrependClass = config('enum-generator.default-prepend-class', '');
        $this->defaultPrependName = config('enum-generator.default-prepend_name', '');
        $this->enumPath = config('enum-generator.enumPath', app_path() . '/Models/Enums');
        $this->tableNames = config('enum-generator.tables');
    }

    private function log($msg): void
    {
        if ($this->commandThis !== null) {
            $this->commandThis->info($msg);
        }
        info($msg);
    }

    /**
     * @param Command|null $commandThis
     * @throws Throwable
     */
    public function create(?Command $commandThis = null): void
    {
        $this->commandThis = $commandThis;

        foreach ($this->tableNames as $tableName => $tableOptions) {
            //Fix previous config format to be more consistent
            if (isset($tableOptions['prepend_class'])) {
                $tableOptions['prepend-class'] = $tableOptions['prepend_class'];
            }
            if (isset($tableOptions['prepend_name'])) {
                $tableOptions['prepend-name'] = $tableOptions['prepend_name'];
            }

            $tableOptions['uuid'] = $tableOptions['uuid'] ?? $this->defaultUseUuid;
            $tableOptions['leave-schema'] = $tableOptions['leave-schema'] ?? $this->defaultLeaveSchema;
            $tableOptions['prepend-class'] = $tableOptions['prepend-class'] ?? $this->defaultPrependClass;
            $tableOptions['prepend-name'] = $tableOptions['prepend-name'] ?? $this->defaultPrependName;

            $this->log('Creating enum for ' . $tableName);

            $sql = "select id, name";

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
                    Str::slug(
                        str_replace(
                            [
                                '/',
                                '+',
                            ],
                            [
                                '_',
                                '_plus_',
                            ],
                            $enumDataRow->name
                        ),
                        '_'
                    )
                );

                if ($enumDataRow->nameString !== '' && is_numeric($enumDataRow->nameString[0])) {
                    $enumDataRow->nameString = 'N_' . $enumDataRow->nameString;
                }

                if (!empty($tableOptions['prepend-name'])) {
                    $enumDataRow->nameString = strtoupper(
                            $tableOptions['prepend_name']
                        ) .
                        '_' .
                        $enumDataRow->nameString;
                }

                $enumDataRow->nameString = preg_replace(
                    [
                        '/\s+/',
                        '/-+/'
                    ],
                    [
                        '_',
                        '_dash_'
                    ],
                    $enumDataRow->nameString
                );
            }

            $nameSpace = 'App\\' . str_replace(
                    [
                        app_path() . '/',
                        '/',
                    ],
                    [
                        '',
                        '\\',
                    ],
                    $this->enumPath
                );

            $msgHtml = "<?php\n\n" . view(
                    'laravel-enum-generator::enums.enum',
                    [
                        'nameSpace' => $nameSpace,
                        'className' => $className,
                        'tableName' => $tableName,
                        'enumDataRows' => $enumDataRows,
                        'tableOptions' => $tableOptions,
                    ]
                )->render();

            // if it doesn't exist create and make sure it exists
            if (!is_dir($this->enumPath) && !mkdir($this->enumPath, '440') && !is_dir($this->enumPath)) {
                throw new RuntimeException(sprintf('Di rectory "%s" was not created', $this->enumPath));
            }
            file_put_contents($this->enumPath . '/' . $className . '.php', $msgHtml);
        }
    }
}
