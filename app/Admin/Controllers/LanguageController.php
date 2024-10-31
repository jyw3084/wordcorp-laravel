<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Language;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;

class LanguageController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Language(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('code');
            $grid->column('name_zh');
            $grid->column('name_en');
            $grid->column('created_at')->sortable();
        
            $grid->showQuickEditButton();
            $grid->disableEditButton();
            $grid->enableDialogCreate();
            $grid->disableDeleteButton();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id');
        
            });
        });
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     *
     * @return Show
     */
    protected function detail($id)
    {
        return Show::make($id, new Language(), function (Show $show) {
            $show->field('id');
            $show->field('code');
            $show->field('name_zh');
            $show->field('name_en');
            $show->field('created_at');
            $show->field('updated_at');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Language(), function (Form $form) {
            $form->display('id');
            $form->text('code');
            $form->text('name_zh');
            $form->text('name_en');
        
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
