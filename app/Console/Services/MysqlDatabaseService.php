<?php

namespace App\Console\Services;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Sleep;
use Illuminate\Support\Str;

class MysqlDatabaseService
{
    private string $defaultDatabaseName = 'mysql';

    public function createDatabase(string $databaseName) : bool
    {
        /**
         * Se conectará ao banco padrão, "mysql", para criar o outro banco.
         */
        Config::set('database.connections.mysql.database', $this->defaultDatabaseName);

        /**
         * Verifica se a conexão com o servidor do banco foi estabelecida.
         */
        try {
            DB::connection()->getPdo();
        } catch (\Exception) {
            throw new \Exception("Not connected to the database server.");
        }
                
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

    /**
     * Espera até que o banco de dados esteja pronto para receber conexões.
     * 
     * Retorna "true" se o banco estiver pronto e "false" caso atinja a quantidade
     * limite de tentativas.
     * 
     * @param int   $timeoutInSeconds   Tempo de espera entre as tentativas.
     * @param int   $numberOfAttempts   Quantidade de tentativas que serão realizadas.
     * @param bool  $hasNoLimit         Define se não haverá ou não limite para tentativas.
     *                                  Se esse parâmetro for "true", a quantidade 
     *                                  de tentativas é ignorada.
     * @return bool 
     */
    public function waitForDatabase(int $timeoutInSeconds=15, int $numberOfAttempts=3, bool $hasNoLimit = false) : bool
    {
        /**
         * Se conectará ao banco padrão, "mysql", para testar a conexão.
         */
        Config::set('database.connections.mysql.database', $this->defaultDatabaseName);

        $timeoutInSeconds = empty($timeoutInSeconds) || $timeoutInSeconds < 0 
                                ? 15 : $timeoutInSeconds;

        $numberOfAttempts = empty($numberOfAttempts) || $numberOfAttempts < 0 
                                ? 3 : $numberOfAttempts;

        $isDatabaseReady = false;

        do {
            $numberOfAttempts--;

            /**
             * Tentará se conectar ao banco.
             */
            try {
                DB::connection()->getPdo();
                /**
                 * A conexão foi estabelecida com sucesso.
                 */
                $isDatabaseReady = true;
            } catch (\Exception) {
                /**
                 * Se houve algum problema na tentativa de conexão, uma exceção
                 * é lançada.
                 * A conexão com o banco não foi estabelecida, então espera alguns
                 * segundos para a próxima tentativa.
                 */
                Sleep::for($timeoutInSeconds)->seconds();
            }

        /**
         * O lanço se repetirá até houver uma conexão bem-sucedida ou até se
         * esgotarem a quantidade de tentativas.
         */
        } while (! $isDatabaseReady && ($numberOfAttempts > 0 || $hasNoLimit));

        return $isDatabaseReady;
    }
}
