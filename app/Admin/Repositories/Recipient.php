<?php

namespace App\Admin\Repositories;

use App\Models\Recipient as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class Recipient extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
