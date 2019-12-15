<?php

namespace AdrienPoupa\MigrateRoutines\Console;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use stdClass;

/**
 * Class MigrateRoutines
 * @package AdrienPoupa\MigrateRoutines\Console
 */
abstract class MigrateRoutines extends BaseCommand
{
    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $migrationType;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var Str
     */
    protected $str;

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Filesystem $filesystem
     * @param Str $str
     * @return void
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle(Filesystem $filesystem, Str $str)
    {
        $this->filesystem = $filesystem;
        $this->str = $str;

        $this->database = $this->option('database');

        if ($this->database === 'default') {
            $this->database = config('database.connections.mysql.database');
        }

        $this->convert();
    }

    /**
     * Convert the existing routines
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    private function convert()
    {
        $routines = $this->getData();

        if (!$routines) {
            $this->info('Nothing to migrate.');
            return;
        }

        foreach ($routines as $routine) {
            $migrationName = $this->str->snake($routine->name);
            $up = $this->up($routine);
            $down = $this->down($routine);
            $this->write($migrationName, $up, $down);
        }
    }

    /**
     * @return array
     */
    abstract public function getData();

    /**
     * Generate the up function
     * We use unprepared to avoid error 2014
     * @param stdClass $routine
     * @return string
     */
    abstract public function up(stdClass $routine);

    /**
     * Generate the down function
     * @param stdClass $routine
     * @return string
     */
    abstract public function down(stdClass $routine);

    /**
     * Write the migration
     * @param string $migrationName
     * @param string $up
     * @param string $down
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function write(string $migrationName, string $up, string $down)
    {
        $filename = date('Y_m_d_His') . '_create_' . $this->migrationType . '_' . $migrationName . '.php';

        $path = $this->getMigrationPath() . '/' . $filename;

        $content = $this->generateMigrationContent($migrationName, $up, $down);

        $this->filesystem->put($path, $content);

        $this->line("<info>Created Migration:</info> {$filename}");
    }

    /**
     * Insert the migration information into the stub
     * @param string $migrationName
     * @param string $up
     * @param string $down
     * @return string|string[]
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function generateMigrationContent(string $migrationName, string $up, string $down)
    {
        return str_replace(
            ['DummyClass', 'schema_up', 'schema_down'],
            [$this->getClassName($migrationName), $this->indent($up),  $this->indent($down)],
            $this->filesystem->get(__DIR__ . '/stubs/migration.stub')
        );
    }

    /**
     * Get the class name of the new migration file
     * @param string $migrationName
     * @return string
     */
    protected function getClassName(string $migrationName)
    {
        return 'Create' . ucfirst($this->migrationType) . str_replace('_', '', $this->str->title($migrationName));
    }

    /**
     * Indent the migration
     * @param string $text
     * @return mixed
     */
    protected function indent(string $text)
    {
        return str_replace("\n", "\n                    ", $text);
    }

    /**
     * Escape the double quotes
     * @param string $text
     * @return mixed
     */
    protected function escape(string $text)
    {
        return str_replace('"', '\"', $text);
    }
}
