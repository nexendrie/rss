<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssExtension;
use Nexendrie\Utils\Constants;
use ReflectionClass;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class BaseExtension implements RssExtension
{
    public function getName(): string
    {
        return lcfirst((new ReflectionClass($this))->getShortName());
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
    }

    protected function getElementName(string $baseName): string
    {
        return $this->getName() . ":" . $baseName;
    }

    /**
     * @return array<string, string|string[]>
     */
    protected function getElementTypes(): array
    {
        return [];
    }

    protected function registerElements(OptionsResolver $resolver): void
    {
        $elements = Constants::getConstantsValues(static::class, "ELEMENT_");
        $elementTypes = $this->getElementTypes();
        array_walk($elements, function (string $value) use ($resolver, $elementTypes): void {
            $resolver->setDefined($this->getElementName($value));
            if (array_key_exists($value, $elementTypes)) {
                $resolver->setAllowedTypes($this->getElementName($value), $elementTypes[$value]);
            }
        });
    }
}
