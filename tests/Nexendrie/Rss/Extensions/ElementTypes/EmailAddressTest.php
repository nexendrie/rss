<?php
declare(strict_types=1);

namespace Nexendrie\Rss\Extensions\ElementTypes;

use MyTester\Attributes\Group;
use MyTester\Attributes\TestSuite;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\OptionsResolver;

#[TestSuite("EmailAddress")]
#[Group("elementTypes")]
final class EmailAddressTest extends \MyTester\TestCase
{
    public function testGetName(): void
    {
        $elementType = new EmailAddress();
        $this->assertSame("email", $elementType->getName());
    }

    public function testValidation(): void
    {
        $elementType = new EmailAddress();
        $resolver = new OptionsResolver();
        $resolver->setRequired(["abc",]);
        $resolver->setAllowedTypes("abc", $elementType->getSimpleType()->value);
        $resolver->setAllowedValues("abc", $elementType->getValidator());

        $resolver->resolve(["abc" => "abc@test.localhost",]);
        $resolver->resolve(["abc" => "abc+def@test.localhost",]);
        $resolver->resolve(["abc" => "abc.def@test.localhost",]);
        $resolver->resolve(["abc" => "abc-def@test.localhost",]);
        $resolver->resolve(["abc" => "abc@test.localhost (Test Tester)",]);
        $resolver->resolve(["abc" => "abc@test.localhost (Test Testovič)",]);
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
