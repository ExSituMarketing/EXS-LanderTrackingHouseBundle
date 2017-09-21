# EXS-LanderTrackingHouseBundle

[![Build Status](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingHouseBundle.svg?branch=master)](https://travis-ci.org/ExSituMarketing/EXS-LanderTrackingHouseBundle)

## What is this bundle doing ?

This bundle searches for tracking parameters in the request.

Stores the tracking parameters in cookies.

And then add those tracking parameters to any url using Formatter services.

## Installation

Download the bundle using composer

```
$ composer require exs/lander-tracking-house-bundle
```

Enable the bundle

```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new EXS\LanderTrackingHouseBundle\EXSLanderTrackingHouseBundle(),
        // ...
    );
}
```

## Configuration

The `cmp`, `exid` and `visit` parameters have a default value configurable with those configuration keys :

```yml
# Default values.
exs_lander_tracking_house:
    default_cmp: ~
    default_exid: 'exid'
    default_visit: 1
```

## Usage

### Use the `appendTracking` Twig filter on any url.

If the user have got the required tracking parameters, the tracking parameters will be added.

#### Use a specific formatter

```twig
<a href="{{ 'https://www.foo.tld/bar' | appendTracking('foo') }}" target="_blank">Some link</a>

<a href="{{ url('homepage') | appendTracking('foo') }}" target="_blank">Some link</a>
```

#### Replace placeholders

```twig
<a href="{{ url('homepage', {'cmp': '{cmp}', 'exid': '{exid}'}) | appendTracking }}" target="_blank">Some link</a>

<a href="{{ 'https://www.foo.tld/bar?cmp={cmp}&exid={exid}&visit={visit}' | appendTracking }}" target="_blank">Some link</a>
```

### Use `getTracking('parameterName')` Twig function to get any tracking parameter.

```twig
    <input type="hidden" name="foo" value="{{ getTracking('foo') }}">
```

## Builtin extractor
- `cmp` searches for the parameter named `cmp` in the request or the cookies.
This parameter contains the `cmp`'s value and is extracted as internal parameter `cmp`.
If not found it will define a default value using `exs_lander_tracking_house.default_cmp` configuration's parameter.
- `cup` searches for the parameter named `cup` in the request. This parameter contains a string composed of `{cmp}~{exid}~{product_id}` and is extracted as internal parameters `cmp`, `exid` and `product_id`.
- `cu` searches for the parameter named `cu` in the request. This parameter contains a string composed of `{cmp}~{exid}` and is extracted as internal parameters `cmp` and `exid`.
- `cuvp` searches for the parameter named `cuvp` in the request. This parameter contains a string composed of `{cmp}~{exid}~{visit}~{product_id}` and is extracted as internal parameters `cmp`, `exid`, `visit` and `product_id`.
- `cuv` searches for the parameter named `cuv` in the request. This parameter contains a string composed of `{cmp}~{exid}~{visit}` and is extracted as internal parameters `cmp`, `exid` and `visit`.
- `exid` searches for the parameter named `exid` or `u` or `uuid` in the request or cookies (Will use the first match).
This parameter contains the `exid`'s value and is extracted as internal parameters `exid`.
If not found it will define a default value using `exs_lander_tracking_house.default_exid` configuration's parameter.
- `product_id` searches for the parameter named `p` in the request. This parameter contains the `product_id`'s value and is extracted as internal parameters `product_id`.
- `uvp` searches for the parameter named `uvp` in the request. This parameter contains a string composed of `{exid}~{visit}~{product_id}` and is extracted as internal parameters `exid`, `visit` and `product_id`.
- `uv` searches for the parameter named `uv` in the request. This parameter contains a string composed of `{exid}~{visit}` and is extracted as internal parameters `exid` and `visit`.
- `visit` searches for the parameter named `visit` in the request or cookies.
This parameter contains the `visit`'s value and is extracted as internal parameters `visit`.
If not found it will define a default value using `exs_lander_tracking_house.default_visit` configuration's parameter.

## Builtin formatter
- `cup` will use `cmp`, `exid` and `product_id` internal parameters to append the parameter `cup` composed of `{cmp}~{exid}~{product_id}`.
- `cu` will use `cmp` and `exid` internal parameters to append the parameter `cu` composed of `{cmp}~{exid}`.
- `cuvp` will use `cmp`, `exid`, `visit` and `product_id` internal parameters to append the parameter `cuvp` composed of `{cmp}~{exid}~{visit}~{product_id}`.
- `cuv` will use `cmp`, `exid` and `visit` internal parameters to append the parameter `cuv` composed of `{cmp}~{exid}~{visit}`.
- `uvp` will use `exid`, `visit` and `product_id` internal parameters to append the parameter `uvp` composed of `{exid}~{visit}~{product_id}`.
- `uv` will use `exid` and `visit` internal parameters to append the parameter `uv` composed of `{exid}~{visit}`.

## Adding an extracter

The bundle uses extracter services to find and get tracking parameters from the request, the cookies and/or define default value. 

Those services have to implement `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterQueryExtracterInterface` and/or `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterCookieExtracterInterface` and/or `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterInitializerInterface`.

All those methods have to return a key/value array of tracking parameters found that will be saved in cookies.

### Example :

#### 1. Creating the extractor class for a new parameter `foo`

```php
<?php

namespace My\SomeBundle\Service;

use Symfony\Component\HttpFoundation\ParameterBag;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterCookieExtracterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterQueryExtracterInterface;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterInitializerInterface;

class FooExtracter implements TrackingParameterCookieExtracterInterface, TrackingParameterQueryExtracterInterface, TrackingParameterInitializerInterface
{
    public function extractFromQuery(ParameterBag $query)
    {
        $trackingParameters = [];

        if (null !== $foo = $query->get('foo')) {
            $trackingParameters['foo'] = $foo;
        }

        return $trackingParameters;
    }
    
    public function extractFromCookies(ParameterBag $cookies)
    {
        $trackingParameters = [];

        if (null !== $foo = $cookies->get('foo')) {
            $trackingParameters['foo'] = $foo;
        }

        return $trackingParameters;
    }
    
    public function initialize()
    {
        return [
            'foo' => 123,
        ];
    }
}

```

_Important thing to notice here :_ All keys from the array returned by an `extractFromQuery()`, `extractFromCookies()` and `initialise()` will be stored as a cookie.

In the example ahead, a cookie named `foo` will be stored with the value found in query or in cookies or else will define the default value. 

#### 2. Declare the service with tag `exs_tracking.parameter_extracter`

```yml
services:
    exs_tracking.foo_extracter:
        class: 'My\SomeBundle\Service\FooExtracter'
        tags:
            - { name: 'exs_tracking.parameter_extracter' }

```

In case many extracters have an `initialise()` method defined, we can specify a priority to know which default value to use (Higher value wins).

```yml
services:
    exs_tracking.foo_extracter:
        class: 'My\SomeBundle\Service\FooExtracter'
        tags:
            - { name: 'exs_tracking.parameter_extracter', priotiry: 100 }

```

## Adding a formatter

The bundle uses formatter services to get the parameters to append to an url.

Those services have to implements `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface`.

They have to implement a `format()` method that will receive a `ParameterBag` containing all found tracking parameters from the request.

And it will need to return a key/value array of all formatted parameters to append to the url.

Those services also have to be tagged as `exs_tracking.parameter_formatter`. 

See `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\CmpTrackingParameterFormatter` for an example of formatter.

By convention formatter service's name is `exs_tracking.xxxsomethingxxx_formatter`.

### Example :

#### 1. Create the formatter class that implements `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface`

```php
<?php

namespace My\SomeBundle\Service;

use Symfony\Component\HttpFoundation\ParameterBag;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterFormatterInterface;

class FooFormatter implements TrackingParameterFormatterInterface
{
    public function format(ParameterBag $trackingParameters)
    {
        return [
            'foo' => $trackingParameters->get('foo'),
        ];
    }
}

```

#### 2. Declare the service with tag `exs_tracking.parameter_formatter`

```yml
services:
    exs_tracking.foo_formatter:
        class: 'My\SomeBundle\Service\FooFormatter'
        tags:
            - { name: 'exs_tracking.parameter_formatter' }

```

#### 3. Usage

As seen before, je just now need to use the Twig filter `appendParameter` on any url and the `foo` parameter will be added if defined.

```twig
    <a href="{{ 'http://www.test.tld/' | appendParameter('foo') }}">Some link</a>
    <!-- or -->
    <a href="{{ 'http://www.test.tld/?foo={foo}' | appendParameter}}">Some link</a>
```
