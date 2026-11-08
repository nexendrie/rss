<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Closure;
use Nexendrie\Rss\Extensions\NormalizedElementType;
use Nexendrie\Rss\Extensions\SimpleElementType;
use Nexendrie\Rss\XmlElement;

final class EscapedString implements NormalizedElementType
{
    public function getName(): string
    {
        return "escaped-string";
    }

    public function getSimpleType(): SimpleElementType
    {
        return SimpleElementType::String;
    }

    public function getValidator(): Closure
    {
        return fn () => true;
    }

    public function getNormalizer(): Closure
    {
        return fn (string $name, string $value, ?string $namespaceUri = null) =>
        new XmlElement($name, $value, $namespaceUri, [], true);
    }
}
