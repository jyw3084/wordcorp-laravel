<?php

namespace App\Admin\Controllers;

use App\Admin\Repositories\Payment;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Show;
use Dcat\Admin\Http\Controllers\AdminController;
use App\Admin\Renderable\PaymentTable;
use Dcat\Admin\Widgets\LazyTable;

class PaymentController extends AdminController
{
    private function from($order_deadline) {
        if (date('d', strtotime($order_deadline)) >= 20) {
            return date('Y-m-', strtotime($order_deadline)).'20';
        } else {
            return date('Y-m-', strtotime('-1 month', strtotime($order_deadline))).'20';
        }
    }

    private function to($order_deadline) {
        if (date('d', $order_deadline) >= 20) {
            return date('Y-m-', strtotime('+1 month', $order_deadline)).'19';
        } else {
            return date('Y-m-', $order_deadline).'19';
        }
    }
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Payment(['user']), function (Grid $grid) {
            // $from = $grid->column('order_deadline')->getValue();
            // $grid->model()->selectRaw('SUM(IFNULL(trans,0)) as trans, SUM(IFNULL(edit,0)) as edit, sum(case when trans is not null then total else 0 end) as trans_total, sum(case when edit is not null then total else 0 end) as edit_total , uid, SUM(IFNULL(total,0)) as total')->whereBetween('created_at', [$from, $to])->groupBy('uid');
            // $grid->model()->selectRaw('SUM(IFNULL(trans,0)) as trans, SUM(IFNULL(edit,0)) as edit, SUM(case when trans is not null then total else 0 end) as trans_total, SUM(case when edit is not null then total else 0 end) as edit_total, uid, SUM(IFNULL(total,0)) as total')->whereBetween('order_deadline', [$from, $to])->groupBy('uid');
            // $grid->column('order_deadline')->display(function($order_deadline) {
                // $this->from($order_deadline);
            // });
            $grid->column('order_deadline');
            $grid->column('user.email');
            $grid->column('trans');
            $grid->column('trans_total');
            $grid->column('edit');
            $grid->column('edit_total');
            $grid->column('total');
            $grid->column('history')->display('history')->modal(function ($modal) {
                $modal->title('Payments - '.$this->user->name);
                return LazyTable::make(PaymentTable::make(['uid' => $this->uid]));
            });
            $grid->disableCreateButton();
            $grid->disableRefreshButton();  
            $grid->disableActions();
            $grid->disableBatchActions();
            $grid->disableBatchDelete();
            $grid->disableRowSelector();
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
        return Show::make($id, new Payment(), function (Show $show) {
            $show->field('id');
            $show->field('order_id');
            $show->field('order_number');
            $show->field('order_detail');
            $show->field('translator_id');
            $show->field('fee');
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
        return Form::make(new Payment(), function (Form $form) {
            $form->display('id');
            $form->text('order_id');
            $form->text('order_number');
            $form->text('order_detail');
            $form->text('translator_id');
            $form->text('fee');
            $form->display('created_at');
            $form->display('updated_at');
        });
    }
}
