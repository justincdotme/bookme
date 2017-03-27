@foreach ($properties as $property)
    <h1>{{ $property->name }}</h1>
    <h2>{{ $property->short_description }}</h2>
    <p>Rate: {{ $property->formatted_rate }} <br></p>
    <p>{{ $property->long_description }}</p>
    <p>{!!  nl2br($property->formatted_address) !!}</p>
@endforeach
{{ $properties->links() }}