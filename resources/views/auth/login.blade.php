@extends('layouts.app')

@section('content')
<div class="w3layouts-main"> 
	<div class="bg-layer">
		<h1>Login</h1>
		<div class="header-main">
			<div class="main-icon">
				<span class="fa fa-hand-pointer-o"></span>
			</div>
			<div class="header-left-bottom">
				<form method="POST" action="{{ url('login') }}">
					{{ csrf_field() }}
					<div class="icon1">
						<span class="fa fa-user"></span>
						<input id="username" type="text" name="username" value="{{ old('username') }}" required autofocus placeholder="Username / NIS" />
					</div>
					<div class="icon1">
						<span class="fa fa-lock"></span>
						<input id="password" type="password" name="password" placeholder="Password" required/>
					</div>
					<div class="bottom">
						<input type="hidden" name="cms_users_id" value="{{ $cms_users_id }}" />
						<button class="btn" type="submit">Log In</button>
					</div>
				</form>	
			</div>
		</div>
		
		<!-- copyright -->
		<div class="copyright">
			<p>© 2019 Pilkatos.Tech . All rights reserved.</p>
		</div>
		<!-- //copyright --> 
	</div>
</div>	
@endsection