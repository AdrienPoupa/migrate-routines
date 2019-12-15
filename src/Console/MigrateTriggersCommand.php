<?php

namespace AdrienPoupa\MigrateRoutines\Console;

use Illuminate\Support\Facades\DB;
use stdClass;

/**
 * Class MigrateTriggersCommand
 * @package AdrienPoupa\MigrateRoutines\Console
 */
class MigrateTriggersCommand extends MigrateRoutines
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:triggers {--database=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert the existing triggers into migrations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->migrationType = 'trigger';
    }

    /**
     * @return array
     */
    public function getData()
    {
        return DB::select("SELECT TRIGGER_NAME AS `name`, ACTION_TIMING, EVENT_MANIPULATION, EVENT_OBJECT_TABLE,
                                         ACTION_ORIENTATION, ACTION_STATEMENT
                                  FROM INFORMATION_SCHEMA.TRIGGERS WHERE TRIGGER_SCHEMA = '$this->database'");
    }

    /**
     * Generate the up function
     * We use unprepared to avoid error 2014
     * @param stdClass $trigger
     * @return string
     */
    public function up(stdClass $trigger)
    {
        return 'DB::unprepared("CREATE TRIGGER `' . $trigger->name . '`
                '. $trigger->ACTION_TIMING .' '. $trigger->EVENT_MANIPULATION .' ON  '. $trigger->EVENT_OBJECT_TABLE .'
                FOR EACH ' . $trigger->ACTION_ORIENTATION . ' '.$this->escape($trigger->ACTION_STATEMENT).'");';
    }

    /**
     * Generate the down function
     * @param stdClass $trigger
     * @return string
     */
    public function down(stdClass $trigger)
    {
        return 'DB::unprepared("DROP TRIGGER IF EXISTS `'.$trigger->name.'`");';
    }
}
