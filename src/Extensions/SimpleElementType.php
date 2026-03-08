<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

enum SimpleElementType: string
{
    case String = "string";
    case Integer = "int";
    case Float = "float";
    case Boolean = "bool";
    case Callable = "callable";
    case Null = "null";
    case Mixed = "mixed";
    case Array = "array";
    case ArrayOfStrings = "string[]";
    case ArrayOfIntegers = "int[]";
    case ArrayOfFloats = "float[]";
    case ArrayOfBooleans = "bool[]";
    case ArrayOfCallables = "callable[]";
    case ArrayOfNulls = "null[]";
    case ArrayOfArrays = "array[]";
}
