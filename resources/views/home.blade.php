@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row ">
        @foreach ($users as $user)
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <a href="/chat/{{$user->id}}">
                            {{$user->name}}
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
