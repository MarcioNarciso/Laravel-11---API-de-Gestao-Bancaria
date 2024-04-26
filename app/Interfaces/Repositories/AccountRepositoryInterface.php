<?php

namespace App\Interfaces\Repositories;
use App\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Interface que define o repositório das contas.
 */
interface AccountRepositoryInterface {

    /**
     * Busca todas as contas, mas paginadas.
     *
     * @param  int|null  $perPage
     * @param  int|null  $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     *
     * @throws \InvalidArgumentException
     */
    public function paginate(?int $perPage = null, ?int $page = null) : LengthAwarePaginator;

    /**
     * Busca determinada conta pelo seu ID.
     * 
     * @param   string|int      $id
     * @return  null|\App\Models\Account
     */
    public function findById(string|int $id) : ?Account;

    /**
     * Persiste a conta no banco.
     *
     * Se ocorrer algum erro, uma exceção é lançada.
     * 
     * @param   \App\Models\Account $account
     * @return  $this
     * 
     * @throws \App\Exceptions\ErrorPersistingModelException 
     */
    public function save(Account $account) : self;

    /**
     * Exclui/desativa (soft delete) determinada conta.
     * 
     * Se a conta não existir ou já estiver desativada, retorna nulo.
     * 
     * @param   int|string              $id
     * @return  null|\App\Models\Account
     */
    public function deleteById(string|int $id) : ?Account;

}