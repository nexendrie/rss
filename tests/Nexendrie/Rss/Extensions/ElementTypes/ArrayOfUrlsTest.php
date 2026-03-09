<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tester\Assert;

require __DIR__ . "/../../../../bootstrap.php";

/**
 * @author Jakub Konečný
 * @testCase
 */
final class ArrayOfUrlsTest extends \Tester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new ArrayOfUrls();
        Assert::same("url[]", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new ArrayOfUrls();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => ["http://localhost", "http://127.0.0.1",],]);
        Assert::exception(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => 123,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value 123 is expected to be of type "string[]", but is of type "int".'
        );
        Assert::exception(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "test",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "test" is expected to be of type "string[]", but is of type "string".'
        );
        Assert::exception(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "https://test.localhost/abc/",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "https://test.localhost/abc/" is expected to be of type "string[]", but is of type "string".'
        );
    }
}

$test = new ArrayOfUrlsTest();
$test->run();
