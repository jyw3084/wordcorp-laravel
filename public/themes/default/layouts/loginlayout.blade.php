<!DOCTYPE html>
<html lang="<?php echo str_replace('zh', 'zh_TW', app()->getLocale()); ?>">

    <head>
        {!! meta_init() !!}
        <meta name="keywords" content="@get('keywords')">
        <meta name="description" content="@get('description')">
        <meta name="author" content="@get('author')">
    
        <title>@get('title')</title>

        @styles()
        @scripts()
        
    </head>

    <body>
        <input type="hidden" id="base-url" value="{{URL::to('/')}}" />
        <p id="lang_locale" hidden data-id="<?php echo app()->getLocale(); ?>"></p>

        {{-- validations --}}
        <p hidden id="validation_required" data-id="<?php echo trans('frontend.validations.required'); ?>"></p>
        <p hidden id="validation_email" data-id="<?php echo trans('frontend.validations.email'); ?>"></p>
        <p hidden id="validation_minLength" data-id="<?php echo trans('frontend.validations.minLength'); ?>"></p>
        <p hidden id="validation_equalTo" data-id="<?php echo trans('frontend.validations.equalTo'); ?>"></p>

        {{-- login messages --}}
        <p hidden id="modal_success" data-id="<?php echo trans('frontend.messages.modal_success'); ?>"></p>
        <p hidden id="login_success_text" data-id="<?php echo trans('frontend.messages.login_success_text'); ?>"></p>
        <p hidden id="login_error_text" data-id="<?php echo trans('frontend.messages.login_error_text'); ?>"></p>
        
        {{-- forgot password --}}
        <p hidden id="forgot_password_error" data-id="<?php echo trans('frontend.messages.forgot_password_error'); ?>"></p>
        <p hidden id="forgot_password_success" data-id="<?php echo trans('frontend.messages.forgot_password_success'); ?>"></p>

        {{-- registration messages --}}
        <p hidden id="registration_success" data-id="<?php echo trans('frontend.messages.registration_success'); ?>"></p>
        <p hidden id="registration_failed" data-id="<?php echo trans('frontend.messages.registration_failed'); ?>"></p>

        @content()
    </body>

</html>
