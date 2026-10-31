<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Extensions\PodloveSimpleChapters\ChaptersCollection;
use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PodloveSimpleChapters extends BaseExtension
{
    public const string ELEMENT_CHAPTERS = "chapters";

    /**
     * @internal
     */
    public const string NAMESPACE = "http://podlove.org/simple-chapters";

    public function getName(): string
    {
        return "psc";
    }

    public function getNamespace(): string
    {
        return self::NAMESPACE;
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_CHAPTERS => ChaptersCollection::class,
        ];
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
    }
}
