<?php
declare(strict_types=1);

namespace Nexendrie\Rss;

/**
 * @author Jakub Konečný
 * @internal
 * @extends \Nexendrie\Utils\Collection<RssExtension>
 */
final class RssExtensionsCollection extends \Nexendrie\Utils\Collection
{
    public function __construct()
    {
        parent::__construct();
        $this->class = RssExtension::class;
    }
}
