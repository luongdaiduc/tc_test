<?php

namespace App\Repository\Eloquent;

use App\Models\Parcel;
use Illuminate\Support\Collection;

class ParcelRepository extends BaseRepository
{
    /**
     * ParcelRepository constructor.
     *
     * @param Parcel $model
     */
    public function __construct(Parcel $model)
    {
        parent::__construct($model);
    }

    /**
     * get parcel's detail
     *
     * @param $id
     * @return mixed
     */
    public function getDetail($id)
    {
        return $this->model->find($id);
    }

}
