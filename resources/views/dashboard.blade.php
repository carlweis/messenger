@extends('layouts.app')

@section('content')
<div class="container">
	<div class="row">
	<br><br>
		<div class="col-md-8">
			<user-directory :users.sync="users" :user.sync="user"></user-directory>
		</div>
	</div>

</div>

@endsection
