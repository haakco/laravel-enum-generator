
namespace {{ $nameSpace }};

enum {{ $className }}: int
{
@foreach ($enumDataRows as $enumDataRow)
    case {{ $enumDataRow->nameString }} = {{ $enumDataRow->id }};
@endforeach
}
