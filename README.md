# wirecard-bundle
use Hochstrasser\WirecardBundle\Event\ConfirmPaymentEvent;

## Install

Install the package with Composer:

    composer require 'hochstrasserio/wirecard-bundle:dev-master'

Add the bundle to your AppKernel:

```php
// AppKernel.php
$bundles = [
    …
    new Hochstrasser\WirecardBundle\HochstrasserWirecardBundle(),
    …
];
```

## Usage

### Bundle Reference Configuration

```yml
hochstrasser_wirecard:
    # Checkout language, required
    language: ~
    # Customer ID, required
    customer_id: ~
    # Secret, required
    secret: ~
    # Shop ID, optional
    shop_id: ~
    # Password for backend requests, optional
    backend_password: ~
    # User agent used for requests, change this to your app name
    user_agent: "Hochstrasser/Wirecard"
    # Version of DataStorage JS, possible values are null and "pci3"
    javascript_script_version: ~
```

### Retrieving the Wirecard Context

```php
<?php

$context = $this->get('hochstrasser_wirecard.context');
```

### Using the WirecardController to handle confirm requests

This bundle includes an implementation of a controller to handle requests sent to the `confirmUrl` parameter supplied to payment requests.

The `confirmUrl` parameter sets a URL which is updated with a request from Wirecard with payment information and state.

Register the controller in your routing configuration to enable this feature:

```yaml
wirecard_confirm:
    path: /wirecard/confirm
    defaults:
        _controller: hochstrasser_wirecard.wirecard_controller:confirmAction
```

Then pass the URL to the `InitPaymentRequest`:

```php
<?php

$request->setConfirmUrl($this->generateUrl('wirecard_confirm'), [], UrlGeneratorInterface::ABSOLUTE_URL);
```

The controller then checks the responseFingerprint. When the fingerprint is valid, it triggers the `Hochstrasser\WirecardBundle\Event\ConfirmPaymentEvent` in the app's event dispatcher.

Handle this event to implement your business logic, e.g. queuing an order confirmation email.

For example:

```php
<?php

use Hochstrasser\WirecardBundle\Event\ConfirmPaymentEvent;

$listener = function (ConfirmPaymentEvent $event) {
    // Response parameters
    // See: https://guides.wirecard.at/response_parameters
    $data = $event->getData();

    if ($event->isPaymentState(ConfirmPaymentEvent::SUCCESS)) {
        // We got the payment, queue order confirmation email
    } else if ($event->isPaymentState(ConfirmPaymentEvent::FAILURE)) {
        // Notify the user that something went wrong with the payment, and order
        // is on hold
    }
};
```

The `ConfirmPaymentEvent` contains also constants for the Wirecard payment response states:

* `SUCCESS`: Payment was successful, e.g. send order confirmation
* `PENDING`: Payment is still processing
* `CANCEL`: Payment was cancelled by the customer
* `FAILURE`: Payment failure, i.e. notify the user that the order could not be processed
