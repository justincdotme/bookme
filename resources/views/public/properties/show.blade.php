<h1>{{ $property->name }}</h1>
<h2>{{ $property->short_description }}</h2>
<p>Rate: {{ $property->formatted_rate }} <br></p>
<p>{{ $property->long_description }}</p>
<p>{!!  nl2br($property->formatted_address) !!}</p>
@if ($images->count())
    <div id="image-container">
        @foreach($images as $image)

        @endforeach
    </div>
@else
    <span>There are no images for this property.</span>
@endif