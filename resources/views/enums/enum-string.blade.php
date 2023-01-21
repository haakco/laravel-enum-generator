
namespace {{ $nameSpace }};

enum {{ $className }}
{
    case {{ $enumDataRow->nameString }} = '{{ $enumDataRow->name }}';
}
