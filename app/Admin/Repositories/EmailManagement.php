<?php

namespace App\Admin\Repositories;

use App\Models\EmailManagement as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class EmailManagement extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
