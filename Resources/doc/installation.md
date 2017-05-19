Installation
============

## Prerequisites

This version of the bundle requires **Symfony 3.0+**.

## Installation

1. Download MesCryptoBundle using composer
2. Enable the Bundle
3. Configure your application's config.yml

## Step 1. Download MesCryptoBundle using composer

```sh
$ composer require carteni/crypto-bundle
```

## Step 2. Enable the Bundle
```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Mes\Security\CryptoBundle\MesCryptoBundle(),
        // ...
    );
}
```

## Step 3. Configure your application's config.yml

* YAML config version

```yaml
# Enables and generates a random key without authentication secret
mes_crypto: ~
```

```yaml
# Enables and generates a key from an encoded key without authentication secret
mes_crypto:
    key: "your_encoded_key"
```

```yaml
# Enables and generates a key from an encoded key with authentication secret
# The authentication secret can be loaded from a file in ini format (see below)
mes_crypto:
    key: "your_encoded_key"
    secret: "%kernel.secret%"
```

```yaml
# Overrides KeyGenerator, KeyStorage and Encryption default services
mes_crypto:
    key: "your_encoded_key"
    secret: "%kernel.secret%"
    key_storage: your_key_storage_service_id
    key_generator: your_key_generator_service_id
    encryption: your_encryption_service_id
```

```yaml
# The encoded key and the secret can be loaded from a file.
# This bundle uses the default loader (mes_crypto.loader.default service) to load a ini file *.crypto.
mes_crypto:
    key: /home/vagrant/key.crypto # Calls mes_crypto.loader.default->loadKey().
    external_secret: true # Calls mes_crypto.loader.default->loadSecret().
```

```yaml
# The encoded key and the secret can be loaded by a custom loader that must be enabled.
mes_crypto:
    loader: ~

# services.yml
app.crypto_loader:
  class: AppBundle\Security\Crypto\CustomLoader
  public: false
  tags:
    - { name: mes_crypto.loader, resource: 'path/to/any/resource' }
```


* XML config version

```xml
<?xml version="1.0" ?>

<!-- Enables and generates a random key without authentication secret -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:mes-crypto="http://multimediaexperiencestudio.it/schema/dic/crypto"
           xsi:schemaLocation="http://multimediaexperiencestudio.it/schema/dic/crypto
           http://multimediaexperiencestudio.it/schema/dic/crypto/crypto-1.0.xsd">

    <mes-crypto:config />

</container>
```

```xml
<?xml version="1.0" ?>

<!-- Enables and generates a key from an encoded key without authentication secret -->
<!-- The encoded key can be loaded from a file in ini format (see below) -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:mes-crypto="http://multimediaexperiencestudio.it/schema/dic/crypto"
           xsi:schemaLocation="http://multimediaexperiencestudio.it/schema/dic/crypto
           http://multimediaexperiencestudio.it/schema/dic/crypto/crypto-1.0.xsd">

    <mes-crypto:config key="your_encoded_key" />

</container>
```

```xml
<?xml version="1.0" ?>

<!-- Enables and generates a key from an encoded key with authentication secret -->
<!-- The authentication secret can be loaded from a file in ini format (see below) -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:mes-crypto="http://multimediaexperiencestudio.it/schema/dic/crypto"
           xsi:schemaLocation="http://multimediaexperiencestudio.it/schema/dic/crypto
           http://multimediaexperiencestudio.it/schema/dic/crypto/crypto-1.0.xsd">

    <mes-crypto:config key="/home/vagrant/key.crypto" secret="%kernel.secret%"/>

</container>
```

```xml
<?xml version="1.0" ?>

<!-- Overrides KeyGenerator, KeyStorage and Encryption default services -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:mes-crypto="http://multimediaexperiencestudio.it/schema/dic/crypto"
           xsi:schemaLocation="http://multimediaexperiencestudio.it/schema/dic/crypto
           http://multimediaexperiencestudio.it/schema/dic/crypto/crypto-1.0.xsd">

    <mes-crypto:config key="/home/vagrant/key.crypto" secret="%kernel.secret%"
     key-storage="your_key_storage_service_id"
     key-generator="your_key_generator_service_id"
     encryption="your_encryption_service_id" />

</container>
```

```xml
<?xml version="1.0" ?>

<!-- The encoded key and the secret can be loaded from a file.
This bundle uses the default loader (mes_crypto.loader.default service) to load a ini file *.crypto -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:mes-crypto="http://multimediaexperiencestudio.it/schema/dic/crypto"
           xsi:schemaLocation="http://multimediaexperiencestudio.it/schema/dic/crypto
           http://multimediaexperiencestudio.it/schema/dic/crypto/crypto-1.0.xsd">

    <mes-crypto:config key="/home/vagrant/key.crypto" external-secret="true" />

</container>
```

```xml
<?xml version="1.0" ?>

<!-- The encoded key and the secret can be loaded by a custom loader that must be enabled -->
<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xmlns:mes-crypto="http://multimediaexperiencestudio.it/schema/dic/crypto"
           xsi:schemaLocation="http://multimediaexperiencestudio.it/schema/dic/crypto
           http://multimediaexperiencestudio.it/schema/dic/crypto/crypto-1.0.xsd">

    <mes-crypto:config>
        <mes-crypto:loader enabled="true" />
    </mes-crypto:config>

</container>
```

* If you choose to store configurations externally using the default loader, the `ini` format `.crypto` file stores the encoded key and the authentication secret (internally the bundle uses `parse_ini_file` PHP function).
The file extension has to be `.crypto`.

```ini
; key.crypto file
[crypto]
key = your_external_key_encoded
secret = your_external_secret
```

## Generate an authentication secret from console command

```sh
$ bin/console mes:crypto:generate-secret
```

## Generate an encoded key from console command

```sh
$ bin/console mes:crypto:generate-key [--dir=DIR]
```
## Exceptions
The Bundle throws a `Mes\Security\CryptoBundle\Exception` exception.
