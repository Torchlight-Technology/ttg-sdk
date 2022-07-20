# ttg-sdk
A PHP library to interact with TTG's APIs

Install with composer

```
composer require torchlighttechnology/torchlight-sdk:"~1.0"
```

Usage in your project

```php
use torchlighttechnology\TtgSDK;

$api = new TtgSDK(
  'URL', // required
  'USERNAME', // optional
  'PASSWORD' // optional
);

$args = [
  'foo' => 'bar'
];

// your API method must be a dashed route
// calling it here needs to be camelcased
$response = $api->yourExposedApiMethod( // translates to your-exposed-api-method
  json_encode($args), // JSON encoded array
  'POST' // request type of GET, POST, PUT, DELETE
);
```

# Example

## Create a new event on delayed events

```php
use torchlighttechnology\TtgSDK;

$api = new TtgSDK(
  'http://delayedevents/delayed-events/'
);

// Add custom headers
$api->setHeaders( ['x-api-key: abc1234'] );

$args = [
  $callback_uri,
  $parameters,
  $fire_date
];
$response = $api->create(
  json_encode($args),
  'POST'
);
```
