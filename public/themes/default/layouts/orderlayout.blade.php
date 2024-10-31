<!DOCTYPE html>
<html lang="{{ str_replace('zh', 'zh_TW', app()->getLocale()) }}">

    <head>
        {!! meta_init() !!}
        <meta name="keywords" content="@get('keywords')">
        <meta name="description" content="@get('description')">
        <meta name="author" content="@get('author')">
        <meta name="csrf-token" content="{{ csrf_token() }}">
    
        <title>@get('title')</title>

        @styles()
        @scripts()
        
    </head>
    <body>
        <p id="lang_locale" hidden data-id="{{ app()->getLocale() }}"></p>
        <input type="hidden" id="base-url" value="{{URL::to('/')}}" />
        <p hidden id="trans_file" data-id="{{ trans('frontend.order.ntd.upload_files.file') }}"></p>
        <p hidden id="trans_remove" data-id="{{ trans('frontend.order.ntd.upload_files.remove') }}"></p>
        <p hidden id="trans_selectLang" data-id="{{ trans('frontend.order.ntd.upload_files.select_language') }}"></p>
        <p hidden id="trans_from" data-id="{{ trans('frontend.order.ntd.upload_files.from') }}"></p>
        <p hidden id="trans_to" data-id="{{ trans('frontend.order.ntd.upload_files.to') }}"></p>
        <p hidden id="trans_choose_file" data-id="{{ trans('frontend.order.ntd.upload_files.choose_file') }}"></p>
        <p hidden id="trans_selectedLang" data-id="{{ trans('frontend.order.ntd.select_service.selected_languages') }}"></p>
        <p hidden id="trans_wordCount" data-id="{{ trans('frontend.order.ntd.select_service.word_count') }}"></p>
        <p hidden id="trans_expertise" data-id="{{ trans('frontend.order.ntd.select_service.expertise') }}"></p>
        <p hidden id="trans_style" data-id="{{ trans('frontend.order.ntd.select_service.style') }}"></p>
        <p hidden id="trans_notes" data-id="{{ trans('frontend.order.ntd.select_service.notes') }}"></p>
        <p hidden id="trans_service" data-id="{{ trans('frontend.order.ntd.select_service.service') }}"></p>
        <p hidden id="trans_type_of_service" data-id="{{ trans('frontend.order.ntd.select_service.type_of_service') }}"></p>
        <p hidden id="trans_translation" data-id="{{ trans('frontend.order.ntd.order_summary.translation') }}"></p>
        <p hidden id="trans_service_rate" data-id="{{ trans('frontend.order.ntd.order_summary.service_rate') }}"></p>
        <p hidden id="trans_subtotal" data-id="{{ trans('frontend.order.ntd.order_summary.sub_total') }}"></p>
        {{-- qoute part --}}
        <p hidden id="trans_qoute_doc_notes" data-id="{{ trans('frontend.order.qoute.docs_notes_p') }}"></p>
        <p hidden id="trans_qoute_your_notes" data-id="{{ trans('frontend.order.qoute.your_notes') }}"></p>
        <p hidden id="carrier_member" data-id="{{ trans('frontend.invoice.duplicateInvoice.member') }}"></p>
        <p hidden id="carrier_mobile" data-id="{{ trans('frontend.invoice.duplicateInvoice.mobile') }}"></p>
        <p hidden id="style_1" data-id="{{ trans('frontend.order.style.1') }}"></p>
        <p hidden id="style_2" data-id="{{ trans('frontend.order.style.2') }}"></p>
        <p hidden id="style_3" data-id="{{ trans('frontend.order.style.3') }}"></p>
        <p hidden id="option_0" data-id="{{ trans('frontend.order.ntd.select_service.option_0') }}"></p>
        <p hidden id="option_1" data-id="{{ trans('frontend.order.ntd.select_service.option_1') }}"></p>
        <p hidden id="no_need" data-id="{{ trans('frontend.order.expertise.no_need') }}"></p>
        <p hidden id="art" data-id="{{ trans('frontend.order.expertise.art') }}"></p>
        <p hidden id="bussiness" data-id="{{ trans('frontend.order.expertise.bussiness') }}"></p>
        <p hidden id="ad" data-id="{{ trans('frontend.order.expertise.ad') }}"></p>
        <p hidden id="car" data-id="{{ trans('frontend.order.expertise.car') }}"></p>
        <p hidden id="cv" data-id="{{ trans('frontend.order.expertise.cv') }}"></p>
        <p hidden id="certificates" data-id="{{ trans('frontend.order.expertise.certificates') }}"></p>
        <p hidden id="finance" data-id="{{ trans('frontend.order.expertise.finance') }}"></p>
        <p hidden id="game" data-id="{{ trans('frontend.order.expertise.game') }}"></p>
        <p hidden id="legal" data-id="{{ trans('frontend.order.expertise.legal') }}"></p>
        <p hidden id="marketing" data-id="{{ trans('frontend.order.expertise.marketing') }}"></p>
        <p hidden id="medical" data-id="{{ trans('frontend.order.expertise.medical') }}"></p>
        <p hidden id="mobile" data-id="{{ trans('frontend.order.expertise.mobile') }}"></p>
        <p hidden id="patents" data-id="{{ trans('frontend.order.expertise.patents') }}"></p>
        <p hidden id="scientific" data-id="{{ trans('frontend.order.expertise.scientific') }}"></p>
        <p hidden id="it" data-id="{{ trans('frontend.order.expertise.it') }}"></p>
        <p hidden id="technical" data-id="{{ trans('frontend.order.expertise.technical') }}"></p>
        <p hidden id="tourism" data-id="{{ trans('frontend.order.expertise.tourism') }}"></p>
        <p hidden id="word" data-id="{{ trans('frontend.order.word') }}"></p>
        <p hidden id="back" data-id="{{ trans('frontend.order.back') }}"></p>
        <p hidden id="next" data-id="{{ trans('frontend.order.next') }}"></p>
        <p hidden id="language" data-id="{{ trans('frontend.order.ntd.upload_files.language') }}"></p>
        @partial('header')
        @content()


    </body>

</html>
