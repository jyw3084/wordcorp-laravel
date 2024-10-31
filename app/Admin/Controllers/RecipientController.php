<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Recipient;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class RecipientController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Recipient(), function (Grid $grid) {
            $grid->name->sortable()->editable();
            $grid->email->sortable()->editable();
            $grid->column('created_at');
            $grid->column('updated_at')->sortable();
            
            $grid->quickCreate(function (Grid\Tools\QuickCreate $create) {
                $create->text('name');
                $create->text('email');
            });

            $grid->disableCreateButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
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
        return Form::make(new Recipient(), function (Form $form) {
            $form->hidden('id');
            $form->text('name');
            $form->text('email');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
