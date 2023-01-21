
namespace {{ $nameSpace }};

enum {{ $className }}: int
{
    case {{ $enumDataRow->nameString }} = {{ $enumDataRow->id }};
}
