@extends('layouts.app')

@section('title', $pageTitle ?? '')
@section('head')
    @isset($metaDescription)
        <meta name="description" content="{{ $metaDescription }}" />
    @endisset
    @isset($metaKeywords)
        <meta name="keywords" content="{{ $metaKeywords }}" />
    @endisset
    @isset($metaAuthor)
        <meta name="author" content="{{ $metaAuthor }}" />
    @endisset
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $ogTitle ?? ($pageTitle ?? '') }}" />
    @isset($ogDescription)
        <meta property="og:description" content="{{ $ogDescription }}" />
    @endisset
    @isset($ogImage)
        <meta property="og:image" content="{{ $ogImage }}" />
    @endisset
    @isset($articlePublishedTime)
        <meta property="article:published_time" content="{{ $articlePublishedTime }}" />
    @endisset
    <meta name="twitter:card" content="{{ isset($ogImage) ? 'summary_large_image' : 'summary' }}" />
    <meta name="twitter:title" content="{{ $ogTitle ?? ($pageTitle ?? '') }}" />
    @isset($ogDescription)
        <meta name="twitter:description" content="{{ $ogDescription }}" />
    @endisset
    @isset($ogImage)
        <meta name="twitter:image" content="{{ $ogImage }}" />
    @endisset
@endsection

@section('content')
    {!! $content !!}
@endsection
