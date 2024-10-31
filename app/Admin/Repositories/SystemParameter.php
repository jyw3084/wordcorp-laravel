<?php

namespace App\Admin\Repositories;

use App\Models\SystemParameter as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class SystemParameter extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
