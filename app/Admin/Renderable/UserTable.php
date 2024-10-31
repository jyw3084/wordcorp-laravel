<?php

namespace App\Admin\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Admin\Repositories\User;

class UserTable extends LazyRenderable
{
    public function grid(): Grid
    {
        $code = $this->code;
        return Grid::make(new User(), function (Grid $grid) use ($code){
            $grid->model()->where([['language_combination', 'like', '%'.$code.'%'], ['user_type', 3]])->orderBy('id', 'desc');
            $grid->selector(function (Grid\Tools\Selector $selector) {
                $selector->select('roles', 'Roles', [1 => 'Translator', 2 => 'Editor', 3 => 'Both']);
            });
            $grid->column('roles')->select([ 0 => 'Unassigned', 1 => 'Translator', 2 => 'Editor', 3 => 'Both']);
            $grid->column('name')->sortable();
            $grid->column('email')->sortable();

            $grid->paginate(20);
            $grid->disableCreateButton();
            $grid->disableRefreshButton();  
            $grid->disableActions();
            $grid->disableBatchActions();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
        });
    }
}
