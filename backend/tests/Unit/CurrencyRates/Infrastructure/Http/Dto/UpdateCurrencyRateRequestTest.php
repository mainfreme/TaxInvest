<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyRates\Infrastructure\Http\Dto;

use App\CurrencyRates\Infrastructure\Http\Dto\UpdateCurrencyRateRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class UpdateCurrencyRateRequestTest extends TestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        $this->validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();
    }

    #[Test]
    public function itAcceptsValidRequest(): void
    {
        $request = new UpdateCurrencyRateRequest(
            rate: '4.30',
            source: 'NBP',
        );

        self::assertCount(0, $this->validator->validate($request));
    }

    #[Test]
    public function itAcceptsRequestWithoutSource(): void
    {
        $request = new UpdateCurrencyRateRequest(rate: '4.30');

        self::assertCount(0, $this->validator->validate($request));
    }

    #[Test]
    public function itRejectsBlankRate(): void
    {
        $request = new UpdateCurrencyRateRequest(rate: '');

        $violations = $this->validator->validate($request);

        self::assertGreaterThan(0, \count($violations));
        self::assertSame('rate', $violations->get(0)->getPropertyPath());
    }

    #[Test]
    public function itRejectsInvalidRateFormat(): void
    {
        $request = new UpdateCurrencyRateRequest(rate: 'invalid');

        $violations = $this->validator->validate($request);

        self::assertGreaterThan(0, \count($violations));
        self::assertSame('rate', $violations->get(0)->getPropertyPath());
    }

    #[Test]
    public function itRejectsTooLongSource(): void
    {
        $request = new UpdateCurrencyRateRequest(
            rate: '4.30',
            source: \str_repeat('a', 256),
        );

        $violations = $this->validator->validate($request);

        self::assertGreaterThan(0, \count($violations));
        self::assertSame('source', $violations->get(0)->getPropertyPath());
    }
}
