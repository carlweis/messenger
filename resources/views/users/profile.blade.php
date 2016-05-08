@extends('layouts.app')

@section('content')

<section class="user-profile">
	<div class="jumbotron text-center">
		<div class="container">
			<img src="{{ $user->avatar }}" alt="{{ $user->name }}">
			<h1>{{ $user->name }}</h1>
			<p>Join the conversation.</p>
			<p>
				<a  @click="startConversation({{Auth::id()}}, {{$user->id}})" class="btn btn-success">Send Message</a>
			</p>
		</div>
	</div>
</section>
@stop