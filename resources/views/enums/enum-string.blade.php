
namespace {{ $nameSpace }};

enum {{ $className }}: string
{
    use InvokableCases;
@foreach ($enumDataRows as $enumDataRow)
    case {{ $enumDataRow->nameString }} = '{{ $enumDataRow->name }}';
    case {{ $enumDataRow->nameString }}_ID = '{{ $enumDataRow->id }}';
@endforeach
}
