# doctrine-domain-id-types

This package is based on **ramsey/uuid-doctrine** 
https://packagist.org/packages/ramsey/uuid-doctrine

ramsey/uuid-doctrine is a great package which provides new Doctrine column type - **uuid**

Although uuid column is very useful it's not enough explicit for Domain Driven Design.

For DDD best approach is to use ids with meaningful names ex. UserId, CustomerId, ProductId,
instead of Uuid.

This package will help You create new Doctrine column types for aggregates/entities ids.

## Fragment of ramsey's uuid-doctrine documentation:

### Examples

#### Configuration

To configure Doctrine to use ramsey/uuid as a field type, you'll need to set up
the following in your bootstrap:

``` php
\Doctrine\DBAL\Types\Type::addType('uuid', 'Ramsey\Uuid\Doctrine\UuidType');
```
In Symfony:
 ``` yaml
# app/config/config.yml
doctrine:
    dbal:
        types:
            uuid:  Ramsey\Uuid\Doctrine\UuidType
```
In Zend Framework:
```php
<?php 
// module.config.php
use Ramsey\Uuid\Doctrine\UuidType;

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'types' => [
                    UuidType::NAME => UuidType::class,
```

#### Usage

Then, in your models, you may annotate properties by setting the `@Column`
type to `uuid`, and defining a custom generator of `Ramsey\Uuid\UuidGenerator`.
Doctrine will handle the rest.

``` php
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class Product
{
    /**
     * @var \Ramsey\Uuid\UuidInterface
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;

    public function getId()
    {
        return $this->id;
    }
}
```

If you use the XML Mapping instead of PHP annotations.
``` XML
<id name="id" column="id" type="uuid">
    <generator strategy="CUSTOM"/>
    <custom-id-generator class="Ramsey\Uuid\Doctrine\UuidGenerator"/>
</id>
```

You can also use the YAML Mapping.
``` yaml
id:
    id:
        type: uuid
        generator:
            strategy: CUSTOM
        customIdGenerator:
            class: Ramsey\Uuid\Doctrine\UuidGenerator
```

## End of fragment of ramsey's uuid-doctrine documentation

## AggregateId capabilities provided by this package:

```php
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
```

# How to create more explicit id classes:

Create interface for Your id:
```php
<?php


namespace App\domain\id;


use Lukasz93P\DoctrineDomainIdTypes\domainId\AggregateId;

interface ProductId extends AggregateId
{
    // some additional method specific for ProductId if needed
}
```
Create implementation for that interface, extending `Lukasz93P\DoctrineDomainIdTypes\domainId\BaseAggregateId`

```php
<?php


namespace App\infrastructure\persistence\testPackage\id;


use App\domain\id\ProductId;
use Lukasz93P\DoctrineDomainIdTypes\domainId\BaseAggregateId;

class ProductDoctrineId extends BaseAggregateId implements ProductId
{
    // BaseAggregateId class provides implementation for all 
    // methods declared in Lukasz93P\DoctrineDomainIdTypes\domainId\AggregateId    

    // ! IMPORTANT ! do not create constructor for this class because constructor
    // of base class Lukasz93P\DoctrineDomainIdTypes\domainId\BaseAggregateId is private final
}
```

Create custom Doctrine field class extending `Lukasz93P\DoctrineDomainIdTypes\doctrineId\AggregateIdDoctrineFieldType`

```php
<?php


namespace App\infrastructure\persistence\product\id;


use Lukasz93P\DoctrineDomainIdTypes\doctrineId\AggregateIdDoctrineFieldType;

class ProductDoctrineIdType extends AggregateIdDoctrineFieldType
{
    // implement abstract method which should return class name 
    // of Your's id interface implementation
    protected function mappedAggregateIdImplementationClassName(): string
    {
        return ProductDoctrineId::class;
    }

    // implement abstract method which return name of custom Doctrine field
    public function getName()
    {
        return 'product_id';
    }

}
```

Add field to Doctrine entity in the same way as for ramsey/uuid-doctrine field:
```php
    /**
     * @var ProductId
     *
     * @ORM\Id
     * @ORM\Column(type="product_id", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    protected $id;
```

Register custom field type for Doctrine the same way as for ramsey/uuid-doctrine,
described above, ex. for Symfony:
``` yaml
doctrine:
    dbal:
        types:
            # map name of field specified in App\infrastructure\persistence\product\id\ProductDoctrineIdType::getName
            # to Doctrine custom field type class
            product_id: App\infrastructure\persistence\product\id\ProductDoctrineIdType
```

