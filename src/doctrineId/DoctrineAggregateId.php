<?php


namespace Lukasz93P\DoctrineDomainIdTypes\doctrineId;


use Assert\Assertion;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Lukasz93P\DoctrineDomainIdTypes\domainId\AggregateId;

abstract class DoctrineAggregateId extends Type
{
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getGuidTypeDeclarationSQL($fieldDeclaration);
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return AggregateId
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $this->validateAggregateIdStringValue($value);
        /** @var AggregateId $aggregateIdImplementationClassName */
        $aggregateIdImplementationClassName = $this->aggregateIdImplementationClassName();

        return $aggregateIdImplementationClassName::fromString($value);
    }

    private function validateAggregateIdStringValue($value): void
    {
        Assertion::notEmpty($value);
        Assertion::string($value);
    }

    abstract protected function aggregateIdImplementationClassName(): string;

    /**
     * @param AggregateId|string $value
     * @param AbstractPlatform $platform
     * @return string
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if ($value instanceof AggregateId) {
            return $value->toString();
        }

        return (string)$value;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }

}