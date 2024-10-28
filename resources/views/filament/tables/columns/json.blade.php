@if($getState() !== null)
    @foreach($getState() as $variable => $value)
        <p>
            @if($value == null)
                {{$variable}}: ""@if(!$loop->last),@endif&nbsp;
            @else
                {{$variable}}: {{$value}}@if(!$loop->last),@endif&nbsp;
            @endif
        </p>
    @endforeach
@endif
