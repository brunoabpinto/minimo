@extends('layouts.app')

@section('content')
    <section class="container">
        <h1>Hello World</h1>
        <p>Send a value from the page to the controller and render it back.</p>

        <form method="POST" action="/hello">
            <label for="name">Your name</label>
            <input id="name" name="name" type="text" value="{{ $name ?? '' }}" />
            <button type="submit">Send to controller</button>
        </form>

        @if (!empty($name))
            <p>Hello, <strong>{{ $name }}</strong>!</p>
        @endif
    </section>
@endsection
