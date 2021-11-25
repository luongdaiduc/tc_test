<?php
namespace App\Repository;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Interface EloquentRepositoryInterface
 * @package App\Repositories
 */
interface EloquentRepositoryInterface
{
    /**
     * @param array $attributes
     * @return Model
     */
    public function create(array $attributes): Model;

    /**
     * @param array $params
     * @param array $attributes
     * @return bool
     */
    public function update(array $params, array $attributes): bool;

    /**
     * @param array $params
     * @return bool
     */
    public function delete(array $params): bool;

    /**
     * @param array $data
     * @return bool
     */
    public function insert(array $data): bool;

    /**
     * @param $id
     * @return Model
     */
    public function find($id): ?Model;

    /**
     * @return Collection
     */
    public function all(): Collection;
}
