
namespace {{ $nameSpace }};

use ArchTech\Enums\From;
use ArchTech\Enums\InvokableCases;
use ArchTech\Enums\Options;
use ArchTech\Enums\Values;

enum {{ $className }}: int
{
    use From;
    use InvokableCases;
    use Options;
    use Values;

@foreach ($enumDataRows as $enumDataRow)
    case {{ $enumDataRow->nameString }} = {{ $enumDataRow->id }};
@endforeach
}
