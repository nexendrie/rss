<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\PodloveSimpleChapters;

use Nexendrie\Utils\Collection;

/**
 * @extends  Collection<Chapter>
 */
final class ChaptersCollection extends Collection
{
    public function __construct()
    {
        parent::__construct();
        $this->class = Chapter::class;
    }
}
