<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Models\User;
use App\Models\LanguageCombo;
use App\Admin\Renderable\PaymentTable;
use Dcat\Admin\Widgets\LazyTable;

class TranslatorController extends AdminController
{

    public $title;

    protected function title(){
        $this->title = 'Translator';
        return $this->title;
    }
   
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(User::where('user_type', 3), function (Grid $grid) {
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('roles', 'Roles', [1 => 'Translator', 2 => 'Editor', 3 => 'Both']);
            });
            $grid->column('roles')->select([ 0 => 'Unassigned', 1 => 'Translator', 2 => 'Editor', 3 => 'Both']);
            $grid->column('name')->sortable();
            $grid->column('email')->sortable();

            $grid->column('id', 'Payments')->display('more')->modal(function ($modal) {
                $modal->title('Payments - '.$this->name);

                return LazyTable::make(PaymentTable::make(['uid' => $this->id]));
            
            });
        
            $grid->column('phone_number');
            
            
            $grid->column('active')->switch('green', $refresh = false);
        
            $grid->showQuickEditButton();
            $grid->disableEditButton();
            $grid->enableDialogCreate();
            $grid->filter(function (Grid\Filter $filter) {
                $filter->like('name');
                $filter->like('email');
                $filter->equal('phone_number');
        
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
        return Show::make($id, new User(), function (Show $show) {
            $show->field('id');
            $show->field('name');
            $show->field('email');
            $show->field('phone_number');
            $show->field('language_combination');
            $show->field('active');
            $show->field('email_verified_at');
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
        return Form::make(new User(), function (Form $form) {
            $id = $form->getKey();

            $form->hidden('id');
            $form->select('roles')->options([ 0 => 'Unassigned', 1 => 'Translator', 2 => 'Editor', 3 => 'Both'])->required();
            $form->text('name')->required();
            $form->email('email');
            $form->hidden('email_verified_at');
/*
            if($id){
                $form->password('password', trans('admin.password'))
                    ->minLength(5)
                    ->customFormat(function () {
                        return '';
                    });
            }
            else{
                $form->password('password')
                    ->required()
                    ->minLength(5);
                
            }
            $form->password('confirm_password')->same('password');
*/
            $form->ignore(['confirm_password']);
            $form->text('phone_number')->required();

            $form->checkbox('language_combination')
                ->inline(true)
                ->canCheckAll()
                ->options(LanguageCombo::all()->pluck('name', 'code'))
                ->saving(function ($lang) {
                    return json_encode($lang);
                });
           
            $form->switch('active')->color('green');
            $form->hidden('remember_token');
        
            $form->hidden('user_type');
            $form->display('created_at');
            $form->display('updated_at');

            $form->saving(function (Form $form) {
                $form->user_type = 3;
                /*
                if ($form->password && $form->model()->get('password') != $form->password) {
                    $form->password = bcrypt($form->password);
                }
    
                if (! $form->password) {
                    $form->deleteInput('password');
                }
*/
            });
        });
    }
}
