<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Extensions\Syndication\UpdatePeriod;
use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Syndication extends BaseExtension
{
    public const ELEMENT_UPDATE_PERIOD = "updatePeriod";
    public const ELEMENT_UPDATE_FREQUENCY = "updateFrequency";
    public const ELEMENT_UPDATE_BASE = "updateBase";

    public function getName(): string
    {
        return "sy";
    }

    public function getNamespace(): string
    {
        return "http://purl.org/rss/1.0/modules/syndication/";
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_UPDATE_PERIOD), UpdatePeriod::class);
        $resolver->setNormalizer($this->getElementName(self::ELEMENT_UPDATE_PERIOD), function (Options $options, UpdatePeriod $value): string {
            return $value->value;
        });
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_UPDATE_FREQUENCY), "int");
        $resolver->setAllowedValues($this->getElementName(self::ELEMENT_UPDATE_FREQUENCY), function (int $value): bool {
            return ($value >= 1);
        });
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_UPDATE_BASE), "string");
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
    }
}
