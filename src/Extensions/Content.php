<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class Content extends BaseExtension
{
    public const string ELEMENT_ENCODED = "encoded";

    public function getName(): string
    {
        return "content";
    }

    public function getNamespace(): string
    {
        return "http://purl.org/rss/1.0/modules/content/";
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_ENCODED), "string");
    }
}
