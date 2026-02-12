@extends('layouts.app')

@section('title', $frontMatter['title'] ?? '')
@section('head')
    @isset($frontMatter['description'])
        <meta name="description" content="{{ $frontMatter['description'] }}" />
    @endisset
    @isset($frontMatter['keywords'])
        <meta name="keywords" content="{{ $frontMatter['keywords'] }}" />
    @endisset
    @isset($frontMatter['author'])
        <meta name="author" content="{{ $frontMatter['author'] }}" />
    @endisset
    <meta property="og:type" content="article" />
    <meta property="og:title" content="{{ $frontMatter['title'] ?? ($pageTitle ?? '') }}" />
    @isset($frontMatter['description'])
        <meta property="og:description" content="{{ $frontMatter['description'] }}" />
    @endisset
    @isset($frontMatter['image'])
        <meta property="og:image" content="{{ $frontMatter['image'] }}" />
    @endisset
    @isset($frontMatter['publishDate'])
        <meta property="article:published_time" content="{{ $frontMatter['publishDate'] }}" />
    @endisset
    <meta name="twitter:card" content="{{ isset($frontMatter['image']) ? 'summary_large_image' : 'summary' }}" />
    <meta name="twitter:title" content="{{ $frontMatter['title'] ?? ($pageTitle ?? '') }}" />
    @isset($frontMatter['description'])
        <meta name="twitter:description" content="{{ $frontMatter['description'] }}" />
    @endisset
    @isset($frontMatter['image'])
        <meta name="twitter:image" content="{{ $frontMatter['image'] }}" />
    @endisset
@endsection

@section('content')
    {!! $content !!}
@endsection
