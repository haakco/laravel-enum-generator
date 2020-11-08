<?php

namespace HaakCo\LaravelEnumGenerator\Libraries\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

            $this->log('Creating enum for ' . $tableName);

            $sql = 'select id, name';

            if ((!isset($tableOptions['uuid']) && $this->defaultUseUuid) ||
                (isset($tableOptions['uuid']) && $tableOptions['uuid'])) {
                $sql .= ', uuid';
            }
            $sql .= ' from ' . $tableName;
            $enumDataRows = DB::select($sql);

            $className = '';
            foreach (explode('.', $tableName) as $subName) {
                /** @noinspection NotOptimalIfConditionsInspection */
                if ((!isset($tableOptions['leave-schema']) && $this->defaultLeaveSchema) ||
                    (isset($tableOptions['leave-schema']) && $tableOptions['leave-schema'])) {
                    $className .= Str::studly($subName);
                } else {
                    $className = Str::studly($subName);
                }
            }

            $className .= 'Enum';
            if ((!isset($tableOptions['prepend-class']) && !empty($this->defaultPrependClass)) ||
                (isset($tableOptions['prepend-class']) && !empty($tableOptions['prepend-class']))) {
                $className = Str::studly($tableOptions['prepend-class']) . '_' . $className;
            }
            $className = Str::studly($className);

            foreach ($enumDataRows as $enumDataRow) {
                $enumDataRow->nameString = strtoupper($enumDataRow->name);

                if ((!isset($tableOptions['prepend-name']) && !empty($this->defaultPrependName)) ||
                    (isset($tableOptions['prepend-name']) && !empty($tableOptions['prepend-name']))) {
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
                        ''
                    ],
                    $enumDataRow->nameString
                );
            }

            $msgHtml = "<?php\n\n" . view(
                    'laravel-enum-generator::enums.enum',
                    [
                        'className' => $className,
                        'tableName' => $tableName,
                        'enumDataRows' => $enumDataRows,
                        'tableOptions' => $tableOptions,
                    ]
                )->render();

            // if it doesn't exist create and make sure it exists
            if (!is_dir($this->enumPath) && !mkdir($this->enumPath, '440') && !is_dir($this->enumPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->enumPath));
            }
            file_put_contents($this->enumPath . $className . '.php', $msgHtml);
        }
    }
}
