<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TestExtension extends BaseExtension
{
    public const string ELEMENT_ABC = "abc";

    public function getName(): string
    {
        return "test";
    }

    public function getNamespace(): string
    {
        return "http://test";
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
    }
}
