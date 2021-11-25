<?php

namespace App\Repository\Eloquent;

use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseRepository implements EloquentRepositoryInterface
{
    const TYPE_EQUAL        = 'equal';
    const TYPE_NOT_EQUAL    = 'not_equal';
    const TYPE_TIME         = 'time';
    const TYPE_NOT_NULL     = 'not_null';
    const TYPE_IN           = 'in';

    /**
     * @var Model
     */
    protected $model;

    /**
     * BaseRepository constructor.
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * @param array $attributes
     *
     * @return Model
     */
    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    /**
     * @param array $params
     * @param array $attributes
     *
     * @return bool
     */
    public function update(array $params, array $attributes): bool
    {
        $query = $this->model->query();

        foreach ($params as $key => $value) {
            $query = $query->where($key, '=', $value);
        }

        return $query->update($attributes);
    }

    /**
     * @param array $params
     * @param array $attributes
     *
     * @return Model
     */
    public function updateOrCreate(array $params, array $attributes): Model
    {
        return $this->model->updateOrCreate($params, $attributes);
    }

    /**
     * @param array $params
     *
     * @return bool
     */
    public function delete(array $params): bool
    {
        $query = $this->model->query();

        foreach ($params as $key => $value) {
            $query = $query->where($key, '=', $value);
        }

        return $query->delete();
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function insert(array $data): bool
    {
        return $this->model->insert($data);
    }

    /**
     * @param $id
     * @return Model
     */
    public function find($id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * @param array $params
     * @return int
     */
    public function count(array $params)
    {
        $query = $this
            ->prepareQuery($params['filterParams']);

        return $query->count();
    }

    /**
     * @param array $params
     * @return Builder
     */
    public function prepareQuery(array $params)
    {
        $query = $this->model->query();

        foreach ($params as $key => $param) {
            $value = $param['value'];

            if ($value !== null) {
                switch ($param['type']) {
                    case self::TYPE_EQUAL:
                        $query = $query->where($key, $value);
                        break;
                    case self::TYPE_NOT_EQUAL:
                        $query = $query->where($key, '<>', $value);
                        break;
                    case self::TYPE_TIME:
                        if (!empty($value['from'])) {
                            $query = $query->whereDate($key, '>=', $value['from']);
                        }

                        if (!empty($value['to'])) {
                            $query = $query->whereDate($key, '<=', $value['to']);
                        }

                        break;
                    case self::TYPE_NOT_NULL:
                        if ($value) {
                            $query = $query->whereNotNull($key);
                        } else {
                            $query = $query->whereNull($key);
                        }

                        break;
                    case self::TYPE_IN:
                        $query = $query->whereIn($key, explode(',', $value));
                        break;
                    default:
                        $query = $query->where($key, 'like', "%$value%");
                }
            }
        }

        return $query;
    }

}
