namespace {{ $nameSpace }};

class {{ $className }}
{
@foreach ($enumDataRows as $enumDataRow)
    public const {{ $enumDataRow->nameString }}_ID = {{ $enumDataRow->id }};
    public const {{ $enumDataRow->nameString }}_NAME = '{{ $enumDataRow->name }}';
@endforeach
}
