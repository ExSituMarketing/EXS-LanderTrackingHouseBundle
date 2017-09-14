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

## Usage

Use the "track" Twig filter on any url.

If the user have got the required tracking parameters, the tracking parameters will be added.

```twig
<a href="{{ 'https://www.foo.tld/bar' | track }}" target="_blank">Some link</a>

<a href="{{ url('homepage') | track }}" target="_blank">Some link</a>
```

We can also for to append some specific parameter.

```twig
<a href="{{ 'https://www.foo.tld/bar?cmp={cmp}&exid={exid}&visit={visit}' | track }}" target="_blank">Some link</a>
```

## Adding an extracter

The bundle uses extracter services to find and get tracking parameters from the request. 

Those services have to implements `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterExtracterInterface`.

They have to implement a `extract()` method that will receive a ParameterBag containing all tracking parameters found in the request.

And it will return a key/value array of tracking parameters found that will be saved in cookies.

### Example :

#### 1. Create the extractor class that implements `EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterExtracterInterface`

```php
<?php

namespace My\SomeBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use EXS\LanderTrackingHouseBundle\Service\TrackingParameterManager\TrackingParameterExtracterInterface;

class FooExtracter implements TrackingParameterExtracterInterface
{
    public function extract(Request $request)
    {
        $trackingParameters = [];

        if (null !== $foo = $request->query->get('foo')) {
            $trackingParameters['foo'] = $foo;
        } elseif ($request->cookies->has('foo')) {
            $trackingParameters['foo'] = $request->cookies->get('foo');
        }

        return $trackingParameters;
    }
}

```

_Important thing to notice here :_ All keys from the array returned by an extract() will be stored as a cookie.

In the example ahead, a cookie named `foo` will be stored with the found value. 

#### 2. Declare the service with tag `exs_tracking.parameter_extracter`
```yml
services:
    exs_tracking.foo_extracter:
        class: 'My\SomeBundle\Service\FooExtracter'
        tags:
            - { name: 'exs_tracking.parameter_extracter' }

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
            'f00' => $trackingParameters->get('foo'),
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
