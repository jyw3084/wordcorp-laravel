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
        @partial('header')
        @content()
        @partial('footer')


    </body>

</html>
