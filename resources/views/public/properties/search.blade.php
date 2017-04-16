@extends('public.layouts.public-main')
@section('title', 'bookMe - Search Properties')
@section('content')
<div id="search-results">
    <div v-for="property in properties">
        <property-preview></property-preview>
    </div>
</div>
<div>
    {{ $properties->links() }}
</div>
@endsection
@push('vars')
<script>
    window.results = {!! json_encode($properties) !!};
</script>
@endpush