<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Closure;
use Nette\Utils\Validators;
use Nexendrie\Rss\Extensions\ElementType;
use Nexendrie\Rss\Extensions\SimpleElementType;

final class Url implements ElementType
{
    public function getName(): string
    {
        return "url";
    }

    public function getSimpleType(): SimpleElementType
    {
        return SimpleElementType::String;
    }

    public function getValidator(): Closure
    {
        return Validators::isUrl(...);
    }
}
