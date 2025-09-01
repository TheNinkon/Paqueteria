@extends('layouts/commonMaster')

@php
  if (isset($pageConfigs)) { \App\Helpers\Helpers::updatePageConfig($pageConfigs); }
  $configData = Helper::appClasses();

  /* Display elements */
  $customizerHidden = $customizerHidden ?? '';
@endphp

@section('layoutContent')
  <!-- Content -->
  @yield('content')
  <!--/ Content -->
@endsection
