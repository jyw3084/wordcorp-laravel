<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use Dcat\Admin\Admin;
use App\Models\User;
use App\Models\LanguageCombo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Exception;
use App\Admin\Renderable\OrderTable;
use Dcat\Admin\Widgets\LazyTable;


class UserController extends AdminController
{
	public $title;
	public $preferred_translator;
	
	protected function title(){
		$this->title = 'Client';
		return $this->title;
	}
	/**
	* Make a grid builder.
	*
	* @return Grid
	*/
	protected function grid()
	{
		return Grid::make(User::where('user_type', 2), function (Grid $grid) {
			$grid->column('id')->sortable();
			$grid->column('name');
			$grid->column('email');
			$grid->column('organization');
			$grid->column('id', 'History')->display('more')->modal(function ($modal) {
				$modal->title('History - '.$this->name);
				return LazyTable::make(OrderTable::make(['email' => $this->email]));
			});
			$grid->showQuickEditButton();
			$grid->disableViewButton();
			$grid->disableEditButton();
			$grid->enableDialogCreate();
			$grid->filter(function (Grid\Filter $filter) {
				$filter->like('organization');
				$filter->where('preferred_translator', function ($query) {
					$_id = User::where('name', 'like', "%{$this->input}%")
					->where('user_type', 3)
					->pluck('id');
					
					foreach($_id as $k => $v){
						$query->where('preferred_translator', $v);
						if($query){
							return $query;
						}
						// return;
					}
				});
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
			$show->field('language_combination');
			$show->field('preferred_translator');
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
		return Form::make(User::where('user_type', 2), function (Form $form) {
			$id = $form->getKey();
			$form->hidden('id');
			$form->array('language_combination', function ($table) {
				$table->select('language')->options(function () {
					return LanguageCombo::all()->pluck('name', 'code');
				})
				->load(['translator'], ['/client/get-translator-by-language-combo']);
				$table->select('translator');
			})->saveAsJson();
			$form->text('name')->required();
			$form->email('email')->required();
			$form->hidden('email_verified_at');
			$form->ignore(['confirm_password']);
			$form->tel('phone_number');
			$form->text('organization');
			$form->hidden('remember_token');
			$form->hidden('user_type');
			$form->hidden('created_at');
			$form->hidden('updated_at');
			$form->saving(function ($form) {
				$form->user_type = 2;
			});
		});
	}
	
	public function getTranslatorByLanguageCombo(Request $request){
		$id = $request->get('q');
		return User::where('language_combination', 'like', '%'.$id.'%')
		->where([['user_type', 3], ['roles', 1], ['active', 1]])
		->orWhere([['user_type', 3], ['roles', 3], ['active', 1]])
		->get(['id', DB::raw('name as text')]);
	}
}