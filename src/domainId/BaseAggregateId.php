<?php


namespace Lukasz93P\DoctrineDomainIdTypes\domainId;


use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

abstract class BaseAggregateId implements AggregateId
{
    /**
     * @var UuidInterface
     */
    private $uuid;

    private final function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public static function generate()
    {
        return new static(Uuid::uuid4());
    }

    public static function fromString(string $string)
    {
        return new static(Uuid::fromString($string));
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function equals(AggregateId $otherAggregateId): bool
    {
        if (!$otherAggregateId instanceof static) {
            return false;
        }

        return $this->toString() === $otherAggregateId->toString();
    }

    public function __toString()
    {
        return $this->toString();
    }

}