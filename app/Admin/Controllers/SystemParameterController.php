<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\SystemParameter;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Admin;
use Dcat\Admin\Layout\Content;
use App\Models;

class SystemParameterController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new SystemParameter(), function (Grid $grid) {
            $grid->setting_id->sortable();
            $grid->setting_name->sortable()->editable();
            $grid->value->sortable()->editable();
            
            $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
                $create->text('setting_id');
                $create->text('setting_name');
                $create->text('value');
            });

            $grid->disableCreateButton();
            $grid->disableActions();
            $grid->disableFilterButton();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        Admin::style(
            <<<CSS

        .box-header, .with-border, .mb-1 {
            display:none
        }
        .flex-wrap {
            display:none !important
        }
        .pull-left {
            display:none !important
        }

CSS
);

        return Form::make(new SystemParameter(), function (Form $form) {
            $form->hidden('id');
            $form->text('key');
            $form->text('setting_id');
            $form->text('setting_name');
            $form->text('value');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
