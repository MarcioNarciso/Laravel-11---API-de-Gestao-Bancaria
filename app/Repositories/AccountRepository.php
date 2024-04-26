<?php

namespace App\Repositories;
use App\Exceptions\ErrorPersistingModelException;
use App\Interfaces\Repositories\AccountRepositoryInterface;
use App\Models\Account;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AccountRepository implements AccountRepositoryInterface
{
    public function paginate(?int $perPage = null, ?int $page = null) : LengthAwarePaginator
    {
        return Account::paginate($perPage, page: $page);
    }

    public function findById(string|int $id) : ?Account
    {
        return Account::find($id);
    }

    public function save(Account $account) : self
    {
        $isAccountSaved = false;

        try {
            $isAccountSaved = $account->save();
        } catch (\Exception) {
            throw new ErrorPersistingModelException("Não foi possível persistir a conta.");
        }

        if (! $isAccountSaved) {
            throw new ErrorPersistingModelException("Não foi possível persistir a conta.");
        }

        return $this;
    }

    public function deleteById(string|int $id) : ?Account
    {
        $account = $this->findById($id);

        if (empty($account)) {
            return null;
        }

        $account->delete();

        return $account;
    }
}
