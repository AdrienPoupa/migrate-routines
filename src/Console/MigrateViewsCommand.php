<?php

namespace AdrienPoupa\MigrateRoutines\Console;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class MigrateViewsCommand
 * @package AdrienPoupa\MigrateRoutines\Console
 */
class MigrateViewsCommand extends MigrateRoutines
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:views {--database=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the existing views into migrations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->migrationType = 'view';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return DB::select("SELECT TABLE_NAME AS `name`, VIEW_DEFINITION AS `definition`
                            FROM information_schema.views WHERE TABLE_SCHEMA='$this->database'");
    }

    /**
     * Generate the up function
     * We use unprepared to avoid error 2014
     * @param stdClass $routine
     * @return string
     */
    public function up(stdClass $routine)
    {
        return 'DB::statement("CREATE VIEW `'.$routine->name.'` AS
                '.$this->escape(str_replace('`' . $this->database . '`.', '', $routine->definition)).'");';
    }

    /**
     * Generate the down function
     * @param stdClass $routine
     * @return string
     */
    public function down(stdClass $routine)
    {
        return 'DB::statement("DROP VIEW IF EXISTS `'.$this->escape($routine->name).'`");';
    }
}
