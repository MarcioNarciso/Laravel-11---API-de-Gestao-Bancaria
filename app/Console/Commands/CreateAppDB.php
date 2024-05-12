<?php

namespace App\Console\Commands;

use App\Console\Services\MysqlDatabaseService;
use Illuminate\Console\Command;

class CreateAppDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:create-db';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates the database used by the application.';

    /**
     * Execute the console command.
     */
    public function handle(MysqlDatabaseService $mysqlDatabaseService)
    {
        $databaseName = env('DB_DATABASE');

        if (empty($databaseName)) {
            return $this->warn('The database name is not defined.');
        }

        $isDbCreated = false;

        try {
            $isDbCreated = $mysqlDatabaseService->createDatabase($databaseName);
        } catch (\Exception $e) {
            return $this->error($e->getMessage());
        }
        
        if ($isDbCreated) {
            return $this->info("The database '{$databaseName}' was created successfully.");
        }

        return $this->error("Unable to create the database '{$databaseName}'.");
    }
}
