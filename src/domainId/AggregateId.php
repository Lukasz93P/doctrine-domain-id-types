<?php


namespace Lukasz93P\DoctrineDomainIdTypes\domainId;


interface AggregateId
{
    /**
     * @return AggregateId
     */
    public static function generate();

    /**
     * @param string $string
     * @return AggregateId
     */
    public static function fromString(string $string);

    public function toString(): string;

    public function equals(AggregateId $otherAggregateId): bool;

}