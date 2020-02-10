@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-3 profile">
              <div class="card">
              <img src="{{ asset($user->avatar ?? 'no-image.png') }}" alt="{{ $user->first_name }}" style="width:100%;">
              <h3>{{ $user->getFirstNameAndLastnameAttribute()  }}</h3>
              <div class="row">
                <div class="col-md-4">Tweets</div>
                <div class="col-md-4">Following</div>
                <div class="col-md-4">Followers</div>
             </div>
             <div class="row">
                <div class="col-md-4">{{ count($user->tweets) }}</div>
                <div class="col-md-4">{{ count($user->following) }}</div>
                <div class="col-md-4">{{ count($user->followers) }}</div>
             </div>
             @if(auth()->user()->isNot($user))
                @if(auth()->user()->isFollowing($user))
                    <a href="{{ route('user.unfollow', $user) }}" class="btn btn-danger">UnFollow</a>
                @else
                    <a href="{{ route('user.follow', $user) }}" class="btn btn-success">Follow</a>
                @endif
            @endif
          </div>
        </div>
            
        </div>
    </div>
</div>
@endsection