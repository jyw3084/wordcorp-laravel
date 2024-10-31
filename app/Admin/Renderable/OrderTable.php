<?php

namespace App\Admin\Renderable;

use Dcat\Admin\Grid;
use Dcat\Admin\Grid\LazyRenderable;
use App\Admin\Repositories\Order;
use Dcat\Admin\Widgets\Table;
use Dcat\Admin\Widgets\Card;
use App\Models\User;

class OrderTable extends LazyRenderable
{
    public function grid(): Grid
    {
        $email = $this->email;
        return Grid::make(new Order(), function (Grid $grid) use ($email){
            $grid->model()->where('email', $email)->orderBy('id', 'desc');
            $grid->column('order_date')
            ->display(function ($order_date) {
                $content = date('Y-m-d', strtotime($order_date));
                return $content;
            });
            $grid->column('associated_docs')
            ->display('more')
            ->expand(function () {
                $file_list = [];
                if($this->associated_docs)
                {
                    $file = '';
                    $list = json_decode($this->associated_docs);
                    foreach($list as $k => $v)
                    {
                        if($v){
                            $idRef = strpos($v->file, 'id');
                            $idRef2 = strpos($v->file, '&');
                            $fileId = substr($v->file, $idRef+3, $idRef2 - ($idRef+3));
                            $filename = (mb_strlen($v->filename) > 20) ? mb_substr($v->filename, 0, 20, 'UTF-8').'...' : $v->filename;
                            $file = '<a href="https://docs.google.com/document/d/'.$fileId.'/edit" target="_blank">'.$filename.'</a><br>';
                        }
                        $translator = isset($v->translator) ? User::find($v->translator)->name : '';
                        $translator_delive = '';
                        if($translator)
                            $translator_delive = $v->translator_deliver_date ?? '<input type="button" class="grid-column-switch" id="'.$this->id.'_trans_'.$v->translator.'" onclick="trans_deliver('.$this->id.', \''.$v->id.'\')" value="Send">';
                        $editor = isset($v->editor) ? User::find($v->editor)->name : '';
                        $editor_delive = '';
                        if($editor)
                            $editor_delive = $v->editor_deliver_date ?? '<input type="button" class="grid-column-switch" id="'.$this->id.'_edit_'.$v->translator.'" onclick="editor_deliver('.$this->id.', \''.$v->id.'\')" value="Send">';
                    
                        $file_list[] =  [
                            $file,
                            $v->language_combination,
                            $translator,
                            $translator_delive,
                            $editor,
                            $editor_delive,
                            $v->word_count,
                            $v->translator_fee,
                            $v->editor_fee,
                            $v->deadline,
                        ];
                    }
                }
                // if(!$this->service_type)
                    $file_list = Table::make(['File', 'Language combination', 'Translator', 'Deliver', 'Editor', 'Deliver', 'Word_count', 'Translator Fee', 'Editor Fee', 'Deadline'], $file_list);
                // else
                //     $file_list = Table::make(['File', 'Language combination', 'Translator', 'Deliver', 'Word_count', 'Fee'], $file_list);

                $content = '<table class="table">
                    <tr>
                        <td>Detail</td>
                        <td>'. $file_list.'</td>
                    </tr>
                </table>';
                $card = new Card($content);
                return $card;
            });

            $grid->column('order_price')->sortable();
            $grid->column('discount');
            $grid->column('total_price');

            $grid->column('deadline')
            ->display(function ($deadline) {
                $content = date('Y-m-d', strtotime($deadline));
                return $content;
            });
            $grid->column('payment_status')->using([0 => 'Unpaid', 1 => 'Paid'])->sortable();
            $grid->column('order_status')->using([0 => 'Pending', 1 => 'Paid', 2 => 'Pending case', 3 => 'Accepted case', 4 => 'Partial completion', 5 => 'Order completion', 6 => 'Cancel'])->sortable();

            $grid->column('delivery_date')
            ->display(function ($delivery_date) {
                $content = date('Y-m-d', strtotime($delivery_date));
                return $content;
            })->sortable();

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
