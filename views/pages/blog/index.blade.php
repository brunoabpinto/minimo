@extends('layouts.app')
@section('content')
    <div class="container">
        @foreach ($posts as $post)
            @if (!$loop->first)
                <hr />
            @endif
            <div>
                <div class="post-item">
                    <a href={{ $post['url'] }}>
                        <img width="860" height="510" src="{{ $post['image'] }}" alt="" />
                    </a>
                    <h2>
                        <a href={{ $post['url'] }}>{{ $post['title'] }}</a>
                    </h2>
                    <p>{{ $post['description'] }}</p>
                    <div class="post-item-footer">
                        <span class="post-item-date">— {{ date('j M Y', $post['publishDate']) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
