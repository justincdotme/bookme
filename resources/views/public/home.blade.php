@extends('public.layouts.public-main')
@section('title', 'bookMe - Home')
@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div id="header-image" class="img-responsive"></div>
        </div>
    </div>
@endsection
@push('vars')
<script>
    window.states = {!! json_encode($states) !!}
</script>
@endpush