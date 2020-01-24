<?php

namespace HaakCo\LaravelEnumGeneratorLibraries\System;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EnumCreateLibrary
{
    private $commandThis;

    private function log($msg)
    {
        if ($this->commandThis !== null) {
            $this->commandThis->info($msg);
        }
        info($msg);
    }

    public function create(?Command $commandThis = null)
    {
        $this->commandThis = $commandThis;
        $tableNames = config('enum.tables');

        foreach ($tableNames as $tableName => $tableOptions) {
            $this->log('Creating enum for ' . $tableName);

            $sql = 'select id, name';

            if ($tableOptions['uuid']) {
                $sql .= ', uuid';
            }
            $sql .= ' from ' . $tableName;
            $enumDataRows = DB::select($sql);

            $className = '';
            foreach (explode('.', $tableName) as $subName) {
                /** @noinspection NotOptimalIfConditionsInspection */
                if (
                    config('enum.default-leave-schema') ||
                    (!empty($tableOptions['leave-schema']) && $tableOptions['leave-schema'])
                ) {
                    $className .= Str::studly($subName);
                } else {
                    $className = Str::studly($subName);
                }
            }

            $className .= 'Enum';
            if (!empty($tableOptions['prepend_class'])) {
                $className = Str::studly($tableOptions['prepend_class']) . '_' . $className;
            }
            $className = Str::studly($className);

            foreach ($enumDataRows as $enumDataRow) {
                $enumDataRow->nameString = strtoupper($enumDataRow->name);

                if (!empty($tableOptions['prepend_name'])) {
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

            $msgHtml = "<?php\n\n" . view('enums.enum', [
                    'className' => $className,
                    'tableName' => $tableName,
                    'enumDataRows' => $enumDataRows,
                    'tableOptions' => $tableOptions,
                ])->render();

            $enumPath = config('enum.enumPath');
            // if it doesn't exist create and make sure it exists
            if (!is_dir($enumPath) && !mkdir($enumPath, '440') && !is_dir($enumPath)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created', $enumPath));
            }
            file_put_contents(app_path() . '/Models/Enum/' . $className . '.php', $msgHtml);
        }
    }
}
