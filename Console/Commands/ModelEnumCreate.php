<?php

namespace HaakCo\LaravelEnumGenerator\Console\Commands;

use HaakCo\LaravelEnumGeneratorLibraries\System\EnumCreateLibrary;
use Illuminate\Console\Command;

class ModelEnumCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modelEnums:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate enum file from table';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle(EnumCreateLibrary $enumCreateLibrary): void
    {
        $enumCreateLibrary->create($this);
    }
}
