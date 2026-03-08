<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Nexendrie\Rss\Extensions\ElementTypes\Url;
use Nexendrie\Rss\Generator;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @see https://www.rssboard.org/creative-commons
 * @see http://backend.userland.com/creativeCommonsRssModule
 */
class CreativeCommons extends BaseExtension
{
    public const string ELEMENT_LICENSE = "license";

    public function getNamespace(): string
    {
        return "http://backend.userland.com/creativeCommonsRssModule";
    }

    protected function getElementTypes(): array
    {
        return [
            self::ELEMENT_LICENSE => "string[]",
        ];
    }

    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedValues($this->getElementName(self::ELEMENT_LICENSE), $this->isArrayOfUrls(...));
    }

    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void
    {
        $this->registerElements($resolver);
        $resolver->setAllowedValues($this->getElementName(self::ELEMENT_LICENSE), $this->isArrayOfUrls(...));
    }

    /**
     * @param list<string> $value
     */
    private function isArrayOfUrls(array $value): bool
    {
        return array_all($value, static fn (string $value): bool => (new Url())->getValidator()($value));
    }
}
