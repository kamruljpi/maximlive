@extends('layouts.dashboard')
@section('page_heading','Restore data')
@section('page_heading_right',  Carbon\Carbon::now()->format('d-m-Y'))
@section('section')
@include('maxim.history.restore_menu')
@endsection