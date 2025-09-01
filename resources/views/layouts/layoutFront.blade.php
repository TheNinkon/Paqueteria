@extends('layouts/commonMaster')
@php
  $configData = Helper::appClasses();
  $isFront = true;
@endphp

@section('layoutContent')

  @include('layouts/sections/navbar/navbar-front')

  <!-- Sections:Start -->
  @yield('content')
  <!-- / Sections:End -->

  @include('layouts/sections/footer/footer-front')
@endsection
