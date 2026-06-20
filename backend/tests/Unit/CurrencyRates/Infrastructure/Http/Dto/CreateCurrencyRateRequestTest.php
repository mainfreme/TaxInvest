<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyRates\Infrastructure\Http\Dto;

use App\CurrencyRates\Infrastructure\Http\Dto\CreateCurrencyRateRequest;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateCurrencyRateRequestTest extends TestCase
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
        $request = new CreateCurrencyRateRequest(
            baseCurrency: 'PLN',
            targetCurrency: 'EUR',
            rate: '4.25',
            effectiveDate: '2025-06-18',
            source: 'NBP',
        );

        self::assertCount(0, $this->validator->validate($request));
    }

    #[Test]
    public function itAcceptsRequestWithoutSource(): void
    {
        $request = new CreateCurrencyRateRequest(
            baseCurrency: 'USD',
            targetCurrency: 'PLN',
            rate: '4',
            effectiveDate: '2025-06-18',
        );

        self::assertCount(0, $this->validator->validate($request));
    }

    #[Test]
    public function itRejectsInvalidBaseCurrency(): void
    {
        $request = new CreateCurrencyRateRequest(
            baseCurrency: 'EURO',
            targetCurrency: 'PLN',
            rate: '4.25',
            effectiveDate: '2025-06-18',
        );

        $violations = $this->validator->validate($request);

        self::assertGreaterThan(0, \count($violations));
        self::assertSame('baseCurrency', $violations->get(0)->getPropertyPath());
    }

    #[Test]
    public function itRejectsInvalidRate(): void
    {
        $request = new CreateCurrencyRateRequest(
            baseCurrency: 'PLN',
            targetCurrency: 'EUR',
            rate: '-1',
            effectiveDate: '2025-06-18',
        );

        $violations = $this->validator->validate($request);

        self::assertGreaterThan(0, \count($violations));
        self::assertSame('rate', $violations->get(0)->getPropertyPath());
    }

    #[Test]
    public function itRejectsInvalidEffectiveDate(): void
    {
        $request = new CreateCurrencyRateRequest(
            baseCurrency: 'PLN',
            targetCurrency: 'EUR',
            rate: '4.25',
            effectiveDate: '18-06-2025',
        );

        $violations = $this->validator->validate($request);

        self::assertGreaterThan(0, \count($violations));
        self::assertSame('effectiveDate', $violations->get(0)->getPropertyPath());
    }

    #[Test]
    public function itRejectsTooLongSource(): void
    {
        $request = new CreateCurrencyRateRequest(
            baseCurrency: 'PLN',
            targetCurrency: 'EUR',
            rate: '4.25',
            effectiveDate: '2025-06-18',
            source: \str_repeat('a', 256),
        );

        $violations = $this->validator->validate($request);

        self::assertGreaterThan(0, \count($violations));
        self::assertSame('source', $violations->get(0)->getPropertyPath());
    }
}
