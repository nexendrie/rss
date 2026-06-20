<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("Url")]
#[Group("elementTypes")]
final class UrlTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new Url();
        $this->assertSame("url", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new Url();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => "http://localhost",]);
        $resolver->resolve(["abc" => "http://127.0.0.1",]);
        $resolver->resolve(["abc" => "http://test.localhost",]);
        $resolver->resolve(["abc" => "http://test.localhost/abc",]);
        $resolver->resolve(["abc" => "http://test.localhost/abc/",]);
        $resolver->resolve(["abc" => "http://test.localhost/abc/#def",]);
        $resolver->resolve(["abc" => "https://localhost",]);
        $resolver->resolve(["abc" => "https://127.0.0.1",]);
        $resolver->resolve(["abc" => "https://test.localhost",]);
        $resolver->resolve(["abc" => "https://test.localhost/abc",]);
        $resolver->resolve(["abc" => "https://test.localhost/abc/",]);
        $resolver->resolve(["abc" => "https://test.localhost/abc/#def",]);
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => 123,]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value 123 is expected to be of type "string", but is of type "int".'
        );
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "test",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "test" is invalid.'
        );
    }
}
