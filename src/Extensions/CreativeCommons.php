<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreativeCommons extends BaseExtension
{
    public const string ELEMENT_LICENSE = "license";

    public function getName(): string
    {
        return "creativeCommons";
    }

    public function getNamespace(): string
    {
        return "http://backend.userland.com/creativeCommonsRssModule";
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_LICENSE), "string[]");
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedTypes($this->getElementName(self::ELEMENT_LICENSE), "string[]");
    }
}
