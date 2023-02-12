
namespace {{ $nameSpace }};

enum {{ $className }}: string
{
@foreach ($enumDataRows as $enumDataRow)
    case {{ $enumDataRow->nameString }} = '{{ $enumDataRow->name }}';
    case {{ $enumDataRow->nameString }}_ID = '{{ $enumDataRow->id }}';
@endforeach
}
