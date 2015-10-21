# wirecard-bundle

## Install

Install the package with Composer:

    composer require 'hochstrasserio/wirecard-bundle:dev-master'

Add the bundle to your AppKernel:

```php
// AppKernel.php
$bundles = [
    …
    new Hochstrasser\WirecardBundle\WirecardBundle(),
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
$context = $this->get('hochstrasser_wirecard.context');
```
