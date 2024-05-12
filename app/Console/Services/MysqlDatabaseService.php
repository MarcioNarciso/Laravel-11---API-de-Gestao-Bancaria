<?php

namespace App\Console\Services;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class MysqlDatabaseService
{
    public function createDatabase(string $databaseName) : bool
    {
        /**
         * Se conecta ao banco padrão, "mysql", para criar o outro banco.
         */
        Config::set('database.connections.mysql.database', 'mysql');
                
        try {
            /**
             * Cria o banco desejado.
             */
            return Schema::createDatabase($databaseName);

        } catch (QueryException $e) {
            $message = $e->getMessage();

            /**
             * Verifica se o banco que se deseja criar já existe.
             */
            $isDBAlreadyExists = Str::contains($message, 'database exists');

            if ($isDBAlreadyExists) {
                throw new \Exception("The database '{$databaseName}' already exists.");
            }

            throw new \Exception("Unable to create the database '{$databaseName}'.");
        }
    }
}
