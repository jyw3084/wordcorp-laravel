<?php

namespace App\Admin\Controllers;

use Dcat\Admin\Admin;
use App\VendorAdmin\Metrics\Examples;
use App\Http\Controllers\Controller;
use Dcat\Admin\Http\Controllers\Dashboard;
use Dcat\Admin\Layout\Column;
use Dcat\Admin\Layout\Content;
use Dcat\Admin\Layout\Row;
use App\Models;
use Dcat\Admin\Widgets\Card;
use Carbon\Carbon;
use Dcat\Admin\Widgets\Table;

class HomeController extends Controller
{
    public function index(Content $content)
    {
        return $content
            ->header('Dashboard')
            ->description("Today's Orders...")
            ->body(function (Row $row) {

                $user = Admin::user();
                $orders = Models\Order::whereDate('order_date', Carbon::today())->get();

                $row->column(3, function (Column $column) use($orders) {
                    $column->row(Card::make('Total Amount', $orders->sum('total_price')));
                });
                $row->column(3, function (Column $column) use($orders) {
                    $column->row(Card::make('New Orders', $orders->count()));
                });
            });
    }
}
