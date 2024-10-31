<?php

namespace App\Admin\Extensions\Form;

use Dcat\Admin\Form\Field;

class CKEditor extends Field
{
    public static $js = [
        '/packages/ckeditor/ckeditor.js',
        '/packages/ckeditor/adapters/jquery.js',
    ];

    protected $view = 'admin.ckeditor';

    public function render()
    {
        $this->script = "$('{$this->getElementClassSelector()}').ckeditor();";

        return parent::render();
    }
}
