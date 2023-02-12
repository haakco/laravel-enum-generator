
namespace {{ $nameSpace }};

use ArchTech\Enums\InvokableCases;

enum {{ $className }}: int
{
    use InvokableCases;
@foreach ($enumDataRows as $enumDataRow)
    case {{ $enumDataRow->nameString }} = {{ $enumDataRow->id }};
@endforeach
}
