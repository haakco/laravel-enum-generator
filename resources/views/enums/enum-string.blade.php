
namespace {{ $nameSpace }};

use ArchTech\Enums\InvokableCases;

enum {{ $className }}: string
{
    use InvokableCases;

@foreach ($enumDataRows as $enumDataRow)
    case {{ $enumDataRow->nameString }}_ID = '{{ $enumDataRow->id }}';
    case {{ $enumDataRow->nameString }} = '{{ $enumDataRow->name }}';

@endforeach
}
