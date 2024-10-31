<?php

namespace App\Admin\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Admin\Repositories\Payment;

class PaymentTable extends LazyRenderable
{
    public function grid(): Grid
    {
        $id = $this->uid;
        return Grid::make(new Payment(), function (Grid $grid) use ($id){
            $grid->model()->where('uid', $id)->orderBy('id', 'desc');
            $grid->column('created_at', 'delivered at')->display(function($created_at){
                return date('Y-m-d', strtotime($created_at));
            });
            $grid->column('filename');
            $grid->column('lang_combo', 'Type');
            $grid->column('edit', 'words edited');
            $grid->column('trans', 'words translated');
            $grid->column('total');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->panel();
            
                $filter->between('created_at')->date();
            });

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
