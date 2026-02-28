<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use BackedEnum;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\RssExtension;
use Nexendrie\Utils\Constants;
use ReflectionClass;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UnitEnum;

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

    /**
     * @return list<string>
     */
    protected function getRequiredElements(): array
    {
        return [];
    }

    protected function registerElements(OptionsResolver $resolver): void
    {
        $elements = Constants::getConstantsValues(static::class, "ELEMENT_");
        $requiredElements = $this->getRequiredElements();
        $elementTypes = $this->getElementTypes();
        array_walk($elements, function (string $value) use ($resolver, $elementTypes, $requiredElements): void {
            if (in_array($value, $requiredElements, true)) {
                $resolver->setRequired($this->getElementName($value));
            } else {
                $resolver->setDefined($this->getElementName($value));
            }
            if (array_key_exists($value, $elementTypes)) {
                $elementType = $elementTypes[$value];
                $resolver->setAllowedTypes($this->getElementName($value), $elementType);
                if (is_string($elementType) && $this->isEnumType($elementType)) {
                    $resolver->setNormalizer(
                        $this->getElementName($value),
                        static function (Options $options, UnitEnum $value): string {
                            return $value instanceof BackedEnum ? (string) $value->value : $value->name;
                        }
                    );
                }
            }
        });
    }

    private function isEnumType(string $type): bool
    {
        $builtInTypes = ["string", "int", "float", "array", "bool", "array", "null", "callable",];
        return !in_array($type, $builtInTypes, true) && !str_ends_with($type, "[]") && enum_exists($type);
    }
}
