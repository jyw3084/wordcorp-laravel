<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\EmailManagement;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Storage;

class EmailManagementController extends AdminController
{

    public $title;

    protected function title(){
        $this->title = 'Email Management';
        return $this->title;
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new EmailManagement(), function (Grid $grid) {
            $grid->column('id')->sortable();
            $grid->column('template_name');
            $grid->column('sender_name');
            $grid->column('mail_subject_en');
            $grid->column('mail_subject_zh');
        
            $grid->disableBatchActions();
            $grid->disableBatchDelete();
            $grid->disableRefreshButton(); 
            $grid->disableDeleteButton(); 
            $grid->enableDialogCreate();
            $grid->setDialogFormDimensions('60%', '95%');
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
        return Show::make($id, new EmailManagement(), function (Show $show) {
            $show->field('id');
            $show->field('template_name');
            $show->field('logo');
            $show->field('sender_name');
            $show->field('mail_subject');
            $show->field('mail_body');
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
        return Form::make(new EmailManagement(), function (Form $form) {
            $form->display('id');
            $form->text('template_name');
            $form->text('sender_name');
            $form->text('mail_subject_en');
            $form->text('mail_subject_zh');
            $form->textarea('mail_body_en');
            $form->textarea('mail_body_zh');
        
            $form->hidden('created_at');
            $form->hidden('updated_at');
        });
    }
}
