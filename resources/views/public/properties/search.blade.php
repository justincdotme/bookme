@extends('public.layouts.public-main')
@section('title', 'bookMe - Search Properties')
@section('content')
    @verbatim
    <div id="search-results">
        <div class="row">
            <property-preview v-for="property in properties" :property="property"></property-preview>
        </div>
        <search-paginator></search-paginator>
    </div>
    @endverbatim
@endsection
@push('vars')
<script>
    window.results = {!! json_encode($properties) !!};
</script>
@endpush
@push('scripts')
<script src="{{ mix('/js/search.js') }}"></script>
@endpush