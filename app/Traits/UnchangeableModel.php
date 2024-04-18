<?php

namespace App\Traits;

trait UnchangeableModel
{
    /**
     * Salva a model no banco de dados.
     * Se ela já existir no banco, não é atualizada.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $this->mergeAttributesFromCachedCasts();

        $query = $this->newModelQuery();

        $saved = false;

        // If the "saving" event returns false we'll bail out of the save and return
        // false, indicating that the save failed. This provides a chance for any
        // listeners to cancel save operations if validations fail or whatever.
        if ($this->fireModelEvent('saving') === false) {
            return false;
        }

        if ($this->exists) {
            $this->previnir(__FUNCTION__);
        }

        // If the model is brand new, we'll insert it into our database and set the
        // ID attribute on the model to the value of the newly inserted row's ID
        // which is typically an auto-increment value managed by the database.
        else {
            $saved = $this->performInsert($query);

            if (! $this->getConnectionName() &&
                $connection = $query->getConnection()) {
                $this->setConnection($connection->getName());
            }
        }

        // If the model is successfully saved, we need to do a few more things once
        // that is done. We will call the "saved" method here to run any actions
        // we need to happen after a model gets successfully saved right here.
        if ($saved) {
            $this->finishSave($options);
        }

        return $saved;
    }

    public function update(array $attributes = [], array $options = [])
    {
        $this->previnir(__FUNCTION__);
    }

    private function previnir(string $nomeDoMetodo): never
    {
        throw new \BadMethodCallException("A operação '{$nomeDoMetodo}' não é permitida. "
                                            .static::class." é uma model imutável, só pode ser persistida uma vez.");
    }
}
