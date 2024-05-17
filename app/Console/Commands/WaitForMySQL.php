<?php

namespace App\Console\Commands;

use App\Console\Services\MysqlDatabaseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Sleep;

class WaitForMySQL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:wait-for-mysql 
                            {--t=15 : The timeout in seconds (default: 15 seconds).} 
                            {--n=2 : Number of attempts (default: 2 attempts.}
                            {--no-limit : Sets the number of attempts to infinity.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Waits until the database is ready to accept connections.';

    /**
     * Execute the console command.
     */
    public function handle(MysqlDatabaseService $mysqlDatabaseService)
    {
        $timeoutInSeconds = $this->option('t');
        $numberOfAttempts = $this->option('n');
        $hasNoLimit = $this->option('no-limit');

        $isDatabaseReady = $mysqlDatabaseService->waitForDatabase($timeoutInSeconds, 
                                                                  $numberOfAttempts,
                                                                  $hasNoLimit);

        if ($isDatabaseReady) {
            $this->info('The MySQL Database is ready!!!'); 
            return 0; // Retorna o código de sucesso
        }

        $this->error('Timed out: The MySQL Database is not ready.');
        return 1; // Retorna o código de erro
    }
}
