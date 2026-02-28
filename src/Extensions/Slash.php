<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Slash
 *
 * @author Jakub Konečný
 * @see https://web.resource.org/rss/1.0/modules/slash/
 */
final class Slash extends BaseExtension
{
    public const string ELEMENT_SECTION = "section";
    public const string ELEMENT_DEPARTMENT = "department";
    public const string ELEMENT_COMMENTS = "comments";
    public const string ELEMENT_HIT_PARADE = "hit_parade";

    public function getNamespace(): string
    {
        return "http://purl.org/rss/1.0/modules/slash/";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_SECTION => "string",
            self::ELEMENT_DEPARTMENT => "string",
            self::ELEMENT_COMMENTS => "int",
            self::ELEMENT_HIT_PARADE => "string",
        ];
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedValues(
            $this->getElementName(self::ELEMENT_HIT_PARADE),
            static fn(string $value): bool => (preg_match('#^\d(,\d)*$#', $value) === 1)
        );
    }
}
