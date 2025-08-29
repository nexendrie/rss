<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions;

use Tester\Assert;

require __DIR__ . "/../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class RssCoreTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $extension = new RssCore();
        Assert::same("", $extension->getName());
    }

    public function testGetNamespace(): void
    {
        $extension = new RssCore();
        Assert::same("", $extension->getNamespace());
    }
}

$test = new RssCoreTest();
$test->run();
