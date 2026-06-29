<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("DateTimeString")]
#[Group("elementTypes")]
final class DateTimeStringTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new DateTimeString();
        $this->assertSame("datetime-string", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new DateTimeString();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => "2026",]);
        $resolver->resolve(["abc" => "2026-06",]);
        $resolver->resolve(["abc" => "2026-06-30",]);
        $resolver->resolve(["abc" => "2026-06-30T12:30",]);
        $resolver->resolve(["abc" => "2026-06-30T12:30:15",]);
        $resolver->resolve(["abc" => "2026-06-30T12:30:15+01:00",]);
        $resolver->resolve(["abc" => "2026-06-30T12:30:15-01:00",]);
        $resolver->resolve(["abc" => "2026-06-30T12:30:15Z",]);
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
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "2026-06-30T",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "2026-06-30T" is invalid.'
        );
        $this->assertThrowsException(
            static function () use ($resolver) {
                $resolver->resolve(["abc" => "2026-06-30T+01",]);
            },
            InvalidOptionsException::class,
            'The option "abc" with value "2026-06-30T+01" is invalid.'
        );
    }
}
