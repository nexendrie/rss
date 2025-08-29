<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * RssExtension
 *
 * @author Jakub Konečný
 */
interface RssExtension
{
    public function getName(): string;
    public function getNamespace(): string;
    public function configureChannelOptions(OptionsResolver $resolver, Generator $generator): void;
    public function configureItemOptions(OptionsResolver $resolver, Generator $generator): void;
}
