<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;

#[TestSuite("RssCore")]
#[Group("extensions")]
final class RssCoreTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $extension = new RssCore();
        $this->assertSame("", $extension->getName());
    }

    public function testGetNamespace(): void
    {
        $extension = new RssCore();
        $this->assertSame("", $extension->getNamespace());
    }
}
