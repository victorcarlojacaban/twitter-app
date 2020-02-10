@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-3 profile">
	    	 <div class="card">
		    	  <img src="{{ asset(auth()->user()->avatar ?? 'no-image.png') }}" alt="{{ auth()->user()->first_name }}" style="width:100%;">
			      <h4>{{ auth()->user()->getFirstNameAndLastnameAttribute()  }}</h4>
			      <div class="row">
			        <div class="col-md-4">Tweets</div>
			        <div class="col-md-4">Following</div>
			        <div class="col-md-4">Followers</div>
		    </div>
		    
		   	<profile-component></profile-component>
		  </div>
        </div>
        <div class="col-md-7 timeline-data">
            <timeline-component></timeline-component>
        </div>
        <div class="col-md-2 people-may-know">
        	<h6><strong>People you might know!</strong></h6>

        	 <div class="col-md-12">
        	 	@if ($users)
	        	 	@foreach ($users as $user)
		        	 	@if(auth()->user()->isNot($user) && !auth()->user()->isFollowing($user))
		        	 	<div>
		        	 		 <img src="{{ asset($user->avatar ?? 'no-image.png') }}" alt="{{ $user->first_name }}" style="width:100%;height: 120px;">
				         	<a href="{{ route('user.show', $user) }}"> {{ $user->getFirstNameAndLastnameAttribute()}} </a>
				            <a href="{{ route('user.follow', $user) }}" class="btn btn-success">Follow</a>
				        <div>
				      	<br/>
				         @endif
				    @endforeach
				@else
					No users list available.
				@endif
		      </div>
        </div>
    </div>
</div>
@endsection