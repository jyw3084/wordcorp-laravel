<?php

namespace App\Admin\Repositories;

use App\Models\LanguageCombo as Model;
use Dcat\Admin\Repositories\EloquentRepository;

class LanguageCombo extends EloquentRepository
{
    /**
     * Model.
     *
     * @var string
     */
    protected $eloquentClass = Model::class;
}
