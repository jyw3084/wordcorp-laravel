<link href="{{ asset('themes/default/assets/css/new-order.css') }}" rel="stylesheet">

<main>
    <div class="container" id="show-qoute">
        <div class="row mt-5" style="text-align: right;">
            <div class="col-md-10 offset-md-1">
                <a href="#" onclick="window.print();">列印報價單</a>
            </div>
        </div>
        <div class="row text-center">
            <div class="col-md-10 offset-md-1">
                <div>
                    <br />
                    <img width="200" src="{{URL::to('themes/default/assets/img/logo-wordcorp_300.png')}}" /><br />
                    <br />
                    <p>一元三思有限公司／統一編號:  24698317</p>
                    <p>新北市淡水區福德里自強路343號地下1層之3</p>
                    <p>窗口聯繫方式: 楊心晴 Mimi Yang / 0936917369 / service@thewordcorp.com</p>
                </div>
                <hr />
                    <h3>報價單</h3>
                <hr />
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 offset-md-1">
                下單日期: <span id="order_date">{{ date( 'Y-m-d h:i A' , strtotime($order_date)) }}</span>
            </div>
            
        </div>
        <div class="row ">
            <div class="col-md-10 offset-md-1">
                <table class="mb-5 billed-project-list table table-bordered">
                    <tbody id="qoute_data">
                    @if(!empty($associated_docs))
                        <tr>
                            <td></td>
                            <td>服務</td>
                            <td>字數</td>
                            <td>價率(NTD/字)</td>
                            <td>小計</Sub></td>
                        </tr>
                        <?php
                            $data = json_decode($associated_docs);
                        ?>
                        @foreach($data as $k => $v)
                        <tr>
                            <td>{{ $v->filename}}</td>
                            <td>{{ $combo[$v->language_combination] }}</td>
                            <td>{{ $v->word_count }}</td>
                            <td>{{ $v->service_rate }}</td>
                            <td>{{ $v->doc_price }}</Sub></td>
                        </tr>
                        @endforeach
                        <tr>
                            <td><a href="/new-order/quote/{{ $order_number ?? '' }}">報價連結</a></td>
                            <td colspan="2">新台幣 {{ !empty($order_number) ? number_format($order_price):'' }} 元</td>
                            <td colspan="2">小計</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2">新台幣 {{ !empty($order_number) ? number_format($discount):'' }} 元</td>
                            <td colspan="2">優惠</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td colspan="2">新台幣 {{ !empty($order_number) ? number_format($total_price):'' }} 元</td>
                            <td colspan="2">總額 (含 5% 營業稅)</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            <span class="">總金額: 新台幣 <span id="total_amount">{{ !empty($order_number) ? number_format($total_price):'' }}</span> 元</span>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 offset-md-1">
                 {{-- @if ($project->bankcode)
            <div class="eight columns">
                匯款方式<br />
                銀行代號: {{$project->bankcode}};<br />
                帳號: {{$project->codeNo}};<br />
            </div>
            @endif --}}
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-1"></div>
           
            <div class="col-md-7">
                <img src="{{URL::to('themes/default/assets/img/stamp1.png')}}" />
                <img src="{{URL::to('themes/default/assets/img/stamp2.png')}}" /><br />
            </div>

            <div class="col-md-3 text-muted text-right">
                時間戳記: {{ !empty($order_date) ? date('Y-m-d H:i:s', strtotime($order_date)) : date('Y-m-d H:i:s') }}
            </div>
            <div class="col-md-1"></div>
            
        </div>
    </div>
</main>
@if(empty($order_number))
<script src="{{ asset('themes/default/assets/js/new-order-quote.js?'.date('YmdHis')) }}"></script>
@endif
