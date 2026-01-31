<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Bridges\NetteDI;

/**
 * @author Jakub Konečný
 * @internal
 */
final class Config
{
    public int $shortenDescription = 150;
    public string $dateTimeFormat = "";
    public string $template = "";
    /** @var class-string<RssExtension>[] */
    public array $extensions = [];
}
