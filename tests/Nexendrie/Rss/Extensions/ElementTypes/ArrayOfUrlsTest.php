<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("ArrayOfUrls")]
#[Group("elementTypes")]
final class ArrayOfUrlsTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new ArrayOfUrls();
        $this->assertSame("url[]", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new ArrayOfUrls();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => ["http://localhost", "http://127.0.0.1",],]);
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => 123,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value 123 is expected to be of type "string[]", but is of type "int".'
        );
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "test",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "test" is expected to be of type "string[]", but is of type "string".'
        );
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "https://test.localhost/abc/",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "https://test.localhost/abc/" is expected to be of type "string[]", but is of type "string".'
        );
    }
}
