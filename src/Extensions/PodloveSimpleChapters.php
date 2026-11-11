<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Extensions\PodloveSimpleChapters\ChaptersCollection;
use Nexendrie\Rss\Generator;
use Nexendrie\Rss\XmlElement;
use Symfony\Component\OptionsResolver\Options;
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
        $resolver->setNormalizer(
            $this->getElementName(self::ELEMENT_CHAPTERS),
            static function (Options $options, ChaptersCollection $value): XmlElement {
                $children = [];
                foreach ($value as $item) {
                    $children[] = new XmlElement(
                        "chapter",
                        null,
                        PodloveSimpleChapters::NAMESPACE,
                        ["start" => $item->start, "title" => $item->title, "href" => $item->href, "image" => $item->image,]
                    );
                }
                return new XmlElement("chapters", namespaceUri: self::NAMESPACE, children: $children);
            }
        );
    }
}
