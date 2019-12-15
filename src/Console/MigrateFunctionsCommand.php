<?php

namespace AdrienPoupa\MigrateRoutines\Console;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class MigrateFunctionsCommand
 * @package AdrienPoupa\MigrateRoutines\Console
 */
class MigrateFunctionsCommand extends MigrateRoutines
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:functions {--database=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the existing functions into migrations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->migrationType = 'procedure';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return DB::select("SELECT name, param_list, body, returns FROM mysql.proc WHERE db='$this->database' and type='FUNCTION' ");
    }

    /**
     * Generate the up function
     * We use unprepared to avoid error 2014
     * @param stdClass $function
     * @return string
     */
    public function up(stdClass $function)
    {
        return 'DB::unprepared("CREATE FUNCTION `'.$function->name.'` (' . $function->param_list . ')
                RETURNS ' . $function->returns . '
                '.$this->escape($function->body).'");';
    }

    /**
     * Generate the down function
     * @param stdClass $function
     * @return string
     */
    public function down(stdClass $function)
    {
        return 'DB::unprepared("DROP FUNCTION IF EXISTS `'.$function->name.'`");';
    }
}
