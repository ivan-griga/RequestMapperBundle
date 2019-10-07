# API request mapper bundle

This small Bundle provides tools for easy map and validate request data (instead of using Symfony forms). 

One annotation allow you to work with all types of data (query parameters or request body parameters) and build the same 400 response structures for all actions. In controller you receive a valid object.

This bundle uses **symfony serializer** for handling request.

**Good for develop RESTful API's.**.

## Installation

Add VangrgRequestMapperBundle by running this command from the terminal at the root of
your Symfony project:

```bash
composer require vangrg/request-mapper-bundle
```

If you use Flex, the bundle is automatically enabled and no further action is required.
Otherwise, to start using the bundle, register it in your application's kernel class:

```php
// app/AppKernel.php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // ...
            new Vangrg\RequestMapperBundle\VangrgRequestMapperBundle(),
            // ...
        ];
    }
}
```

### Override default configuration

```yaml
# config/packages/vangrg_request_mapper.yaml or app/config/config.yml
vangrg_request_mapper:
  validation_response:
    enabled: true # Enable or disable validation error response listener(default: true)
                  # If disabled then ValidationException will be thrown for not valid request data
    format: 'json' # Validation response format (json or xml)
```

## Usage

The  `@RequestParamMapper` annotation calls service to map request data to object. This object is stored as request attribute and can be injected as controller method argument.

#### All annotation parameters:

 - `class` - class name for mapping.
 - `deserializationContext` - deserialization context of **symfony serializer**
 - `toExistedObject` - set to **true** if you want map data to existed object. Default - **false**
 - `validate` - enable or disable validation after data inserting. Default - **true**
 - `validationGroups` - validation groups

```php
use Vangrg\RequestMapperBundle\Annotation\RequestParamMapper;
use FOS\RestBundle\Controller\Annotations as Rest;

/**
 * @Rest\Get("/products")
 * @RequestParamMapper("filter", class="App\FilterDto")
 */
public function getProducts(FilterDto $filter)
{
}
```

You can use the **symfony validator** for object.

```php
use Symfony\Component\Validator\Constraints as Assert;

class FilterDto
{
    // ...

    /**
     * @var string
     *
     * @Assert\NotNull()
     */
    public $name;
    
    /**
     * @Assert\Regex("/^(ASC|DESC)$/i")
     */
    public $sortDirection = 'ASC';  
    
    // ...  
}
```
Request example: `/products?name=car&sortDirection=DESC`

#### Combine with `@ParamConverter`

If you want to update an existed object (**PUT**, **PATCH**) you can use `@ParamConverter` to get object from the database and `@RequestParamMapper` to map data from request to this object. 

```php
use Vangrg\RequestMapperBundle\Annotation\RequestParamMapper;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Rest\Put("/products/{id}")
 *
 * @ParamConverter("product", class="App:Product")
 *
 * @RequestParamMapper(
 *     "product",
 *     class="App\Entity\Product",
 *     toExistedObject=true,
 *     deserializationContext={"groups"={"details"}},
 *     validationGroups={"update_product"}
 * )
 */
public function updateProduct(Product $product)
{
    $this->getDoctrine()->getManager()->flush();
    
    return $product;
}
```

Request body example:
```js
{
    "name": "Car",
    "description": "",
    "tags": [
      /*-------------*/
    ],
/*-------------------*/
}
```
 
Events
======

The following is a list of events you can listen to:

| Event name | Trigger point|
|------------|--------------|
|`vangrg_request_mapper.configuration`|allow dynamic reconfiguration before mapping|
|`vangrg_request_mapper.before_normalize`|possibility to change data before normalization to the object|

#### Example

```php
<?php

namespace App\EventListener;

use Vangrg\RequestMapperBundle\Event\ConfigurationEvent;
use Vangrg\RequestMapperBundle\Event\BeforeNormalizeEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class FooSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ConfigurationEvent::NAME => [
                ['onReadConfiguration'],
            ],
            BeforeNormalizeEvent::NAME => [
                ['modifyMappedData'],
            ],            
        ];
    }
    public function onReadConfiguration(ConfigurationEvent $event)
    {
        $configuration = $event->getConfiguration();

        // do your something
    }

    public function modifyMappedData(BeforeNormalizeEvent $event)
    {
        $data = $event->getData();
        
        // do something
        // $data['start'] = (integer) $data['start'];
        
        $event->setData($data);
    }
}
```