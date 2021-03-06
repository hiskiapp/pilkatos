@extends('layouts.front')
@section('scripts')
<script type="text/javascript">
  @if($user == [])
  $(document).ready(function(){
    swal("Warning", "Token Invalid", "warning");
    location.href="{{ url('/') }}";
  });
  @endif
  $(document).on("click", "#sign-in", function () {
    var _token = '{{ csrf_token() }}';
    var token = '{{ $token }}';
    var password = $('#password').val();
    var password_confirmation = $('#password_confirmation').val();

    $.ajax({
      type : "POST",
      url  : "{{ url('set-password') }}",
      dataType : "JSON",
      data : {_token:_token,
        token:token,
        password:password,
        password_confirmation:password_confirmation
      },
      success: function(data){
        if (data.status == 'error') {
          swal("Gagal", data.message, "warning");
        }else{
          swal("Sukses!", data.message, "success");
          location.href="{{ url('/') }}";
        }
      },
      error: function(data) { 
        console.log(data);
      }
    });
  });
</script>
@endsection
@section('content')
<section class="px-md-5 mx-md-5 text-center white-text grey p-5 z-depth-1" style="background-image: url(https://mdbootstrap.com/img/Photos/Others/gradient1.jpg);">
  <div class="row d-flex justify-content-center">
    <div class="col-xl-8 col-md-10">
      <i class="fas fa-gem fa-2x mb-4"></i>
      <h3 class="font-weight-bold">Selamat Datang, {{ $user->name }}!</h3>
      <p>Silahkan Buat Kata Sandi Anda Yang Digunakan Untuk Masuk Dalam Pemilihan Ketua OSIS Nantinya!</p>
    </div>
  </div>
</section>
<section class="px-md-5 mx-md-5 text-center text-lg-left dark-grey-text">
  <div class="row d-flex justify-content-center">
    <div class="col-md-6">
      <form class="text-center">
        <input type="password" id="password" class="form-control mb-4" placeholder="New Password">
        <input type="password" id="password_confirmation" class="form-control mb-4" placeholder="Confirmation Password">
        <span class="btn btn-info btn-block my-4" id="sign-in">Sign in</span>
      </form>
    </div>
  </div>
</section>
@endsection
@section('header')
<header>
  <nav class="navbar fixed-top navbar-expand-lg navbar-light white scrolling-navbar">
    <div class="container">
      <a class="navbar-brand waves-effect" href="#" target="_blank">
        <img class="mr-2" src="{{ asset('images/logo.png')}}" alt="Logo"> PILKATOS.TECH
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">

      </ul>
      <ul class="navbar-nav nav-flex-icons">
        <li class="nav-item">
          <a href="javascript:void(0)"
          class="nav-link border border-light rounded waves-effect mr-2" target="_blank">
          <i class="fas fa-envelope mr-1"></i> Info
        </a>
      </li>
    </ul>
  </div>
</div>
</nav>
</header>
@endsection