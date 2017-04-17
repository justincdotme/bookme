@extends('public.layouts.public-main')
@section('title', 'bookMe - Search Properties')
@section('content')
    @verbatim
    <div id="search-results">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <search-widget>
                    <h1 slot="header">Search for Properties</h1>
                </search-widget>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <property-preview v-for="property in properties" :property="property"></property-preview>
            </div>
        </div>
        <search-paginator></search-paginator>
    </div>
    @endverbatim
@endsection
@push('vars')
<script>
    window.results = {!! json_encode($properties) !!};
    window.states = {!! json_encode($states) !!}
</script>
@endpush
@push('scripts')
<script src="{{ mix('/js/search.js') }}"></script>
@endpush