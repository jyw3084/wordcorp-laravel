<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\LanguageCombo;
use App\Models\Language;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Admin\Renderable\UserTable;
use Dcat\Admin\Widgets\LazyTable;

class LanguageComboController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new LanguageCombo(), function (Grid $grid) {
            $grid->column('code', 'Code');
            $grid->column('name', 'Lang Combo');
            $grid->column('translation_rate_tw', 'Translation Rate NTD');
            $grid->column('editing_rate_tw', 'Editing Rate NTD');
            $grid->column('translation_rate_us', 'Translation Rate USD');
            $grid->column('editing_rate_us', 'Editing Rate USD');
            $grid->column('translator_pay_rate', 'Translator Pay Rate NTD');
            $grid->column('editor_pay_rate', 'Editor Pay Rate NTD');
            
            $grid->column('id', 'Translators')->display('more')->modal(function ($modal) {
                $modal->title('Translator');
                return LazyTable::make(UserTable::make(['code' => $this->code]));
            
            });

            $grid->column('activated', 'Activated TW')->bool(['1' => true, '0' => false]);
            $grid->column('activated_us', 'Activated US')->bool(['1' => true, '0' => false]);
            $grid->showQuickEditButton();
            $grid->disableViewButton();
            $grid->disableEditButton();
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
        return Show::make($id, new LanguageCombo(), function (Show $show) {
            $show->field('id');
            $show->field('code');
            $show->field('name');
            $show->field('rate');
            $show->field('activated');
            $show->field('activated_us');
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
        return Form::make(new LanguageCombo(), function (Form $form) {
            $form->hidden('id');
            $form->select('language_from')->options(function () {
                return Language::all()->pluck('name_en', 'code');;
            })->required();
            $form->select('language_to')->options(function () {
                return Language::all()->pluck('name_en', 'code');;
            })->required();
            $form->decimal('translation_rate_tw');
            $form->decimal('editing_rate_tw');
            $form->decimal('translation_rate_us');
            $form->decimal('editing_rate_us');
            $form->decimal('translator_pay_rate');
            $form->decimal('editor_pay_rate');
            $form->hidden('code');
            $form->hidden('name');
            $form->switch('activated', 'Activated TW');
            $form->switch('activated_us', 'Activated US');
            $form->hidden('created_at');
            $form->hidden('updated_at');

            $form->saving(function (Form $form) {
                $form->code = $form->language_from . ' -> ' . $form->language_to;

                $from = Language::where('code', $form->language_from)
                    ->pluck('name_en')
                    ->all();

                $to = Language::where('code', $form->language_to)
                    ->pluck('name_en')
                    ->all();

                $form->name = $from[0] . ' to ' . $to[0];
                
            });
        });
    }
}
