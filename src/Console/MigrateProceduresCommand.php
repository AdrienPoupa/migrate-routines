<?php

namespace AdrienPoupa\MigrateRoutines\Console;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class MigrateProceduresCommand
 * @package AdrienPoupa\MigrateRoutines\Console
 */
class MigrateProceduresCommand extends MigrateRoutines
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:procedures {--database=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the existing procedures into migrations';

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
        return DB::select("SELECT name, param_list, body FROM mysql.proc WHERE db='$this->database' and type='PROCEDURE' ");
    }

    /**
     * Generate the up function
     * We use unprepared to avoid error 2014
     * @param stdClass $routine
     * @return string
     */
    public function up(stdClass $routine)
    {
        return 'DB::unprepared("CREATE PROCEDURE `'.$routine->name.'` (
                '.$this->escape($routine->param_list).'
                )
                '.$this->escape($routine->body).'");';
    }

    /**
     * Generate the down function
     * @param stdClass $routine
     * @return string
     */
    public function down(stdClass $routine)
    {
        return 'DB::unprepared("DROP PROCEDURE IF EXISTS `'.$this->escape($routine->name).'`");';
    }
}
