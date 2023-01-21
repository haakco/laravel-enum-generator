
namespace {{ $nameSpace }};

enum $className
{
case DRAFT;
case PUBLISHED;
case ARCHIVED;
}

class {{ $className }}
{
@foreach ($enumDataRows as $enumDataRow)
    case {{ $enumDataRow->nameString }}_ID = {{ $enumDataRow->id }};
@if ($tableOptions['uuid'])
    case {{ $enumDataRow->nameString }}_ENUM = '{{ $enumDataRow->uuid }}';
@endif
    case {{ $enumDataRow->nameString }}_NAME = '{{ $enumDataRow->name }}';
@endforeach
}
