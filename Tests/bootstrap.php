<?php

/*
 * This file is part of the MesCryptoBundle package.
 *
 * (c) Francesco Cartenì <http://www.multimediaexperiencestudio.it/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Generate key.crypto
file_put_contents(__DIR__.'/key.crypto', <<<'EOT'
; key.crypto
[crypto]
key = def10000def50200e3b1352c36aee48ef4cc1eacba941e8e93efd1ccb25ec50e5254142fa27aedd1dd3f4f4e185bd5335ad85cc23b2bb2abf500b32d5f26842e1dfca10a6ec1539ba6e4e0f3523e79c13dd77cd13f5581420ad2cafbe63e5b6a114f61a9c45ed8bfbfcb76ee31973ca835a95a09f6dc9ce6ddfddcc5e6dddded489c0968d200b448e329130f7ced6b951139dd159f45f42799eb3fc7bf0b22b403a5ccae95f0a2e36b2a84de87d05c9fa2620d320f273fa9054304dab0e050d7106e176cab6db178563e2584a13bb1d7d88fd1361d1831c07d0ca866bac110d913c60f350747c88ec1a98d52bebf331413c83328f8d33d3125a096dd7904f082
secret = ThisIsASecret
EOT
);

if (!($loader = @include __DIR__.'/../vendor/autoload.php')) {
    echo <<<'EOT'
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT;
    exit(1);
}
