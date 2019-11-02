<?php


namespace Lukasz93P\DoctrineDomainIdTypes\doctrineId;


use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Lukasz93P\DoctrineDomainIdTypes\domainId\AggregateId;

abstract class AggregateIdDoctrineFieldType extends Type
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
        /** @var AggregateId|string $mappedAggregateIdImplementationClassName */
        $mappedAggregateIdImplementationClassName = $this->mappedAggregateIdImplementationClassName();

        return $mappedAggregateIdImplementationClassName::fromString($value);
    }

    private function validateAggregateIdStringValue($value): void
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('Value cannot be empty.');
        }

        if (!$value instanceof AggregateId && !is_string($value)) {
            throw new \InvalidArgumentException('Value have to be instance of ' . AggregateId::class . ' or string.');
        }
    }

    abstract protected function mappedAggregateIdImplementationClassName(): string;

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