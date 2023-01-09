# Kadena PHP Client

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]

This package includes a simple-to-use client to communicate with the Pact API, as well as classes to generate keys, prepare and create Pact commands and the ability to sign commands in your backend.

Using this package allows you to call things like admin functions in Pact from your PHP backend by creating and signing commands, but it also allows you to create a command in the backend, have a wallet sign it on the frontend, and then call the Pact API in the backend.

> If your users have to sign a command, something like Kadena.js would still be required on the frontend. This is not a complete replacement.

> ⚠️ This package is under active development and has only been released under versions 0.x.x to allow for testing of the Package. During development, breaking changes might be made in minor version upgrades.
> 
> Some features like adding capabilities are still missing.

## Installation

Via Composer

``` bash
composer require kadena-php/client
```

## Usage
### Key Pairs
Key Pairs are used to sign your Pact commands. You can generate a new KeyPair using

```php
$keyPair = KeyFactory::generate();
```
### Metadata
Every command sent to the Kadena API requires a metadata object to be present. This object can be created manually, or be constructed using the `MetadataFactory`. 
The factory will set predefined defaults if certain options are no provided. The defaults and options are as follows:
```php
creationTime: Carbon::now(),
ttl: 7200,
gasLimit: 10000,
chainId: '0',
gasPrice: 1e-8,
sender: ''
```
If we want to create an object with the default options but on a different chain, we can do it like this:
```php
$factory = new MetadataFactory();
$metadata = $factory->withOptions([
    'chainId' => '1',
])->make();
```
If no custom options are required, you can just call `$factory->make()` to create your `Metadata` object.
### Signers
Commands have to be signed before sending them to the Kadena API. To support this, a `Signer` (one or many) has to be created. A signer consists of a public key and optionally a list of capabilities.

Let's create a signer with a public key of `example-key` and the `coin.transfer` capability. As a signer can have multiple or no capabilities, all `Capability` objects should be wrapped in a `CapabilityCollection` object:
```php
// Just as an example, keys should be created using the KeyFactory
$publicKey = new PublicKey(new SignaturePublicKey(new HiddenString('example-key')));

$transferCapability = new Capability(
    name: 'coin.transfer',
    arguments: [
        'address-from',
        'address-to',
        5
    ]    
);

$signer = new Signer(
    publicKey: $publicKey,
    capabilities: new CapabilityCollection($transferCapability) 
)
```
Multiple signers can be wrapped in the `SignerCollection` object.

### Payloads
Payloads are the code to be executed by pact. There are two types of payloads: and execute and a continue payload.
```php
$executePayload new ExecutePayload(
    code: '(+ 1 2)'
);

$continuePayload =  new ContinuePayload(
    pactId: 'pact-id',
    rollback: false,
    step: 0
);
```

### Commands
Commands wrap all data sent to the Kadena API, a `Command` object can be created manually, but it is recommended to use the `CommandFactory` for this.
The factory will set certain defaults, and can be used like this:
```php
$factory = new CommandFactory();

$factory->withExecutePayload($executePayload)
    ->withMetadata($metadata)
    ->withSigners(new SignerCollection($signer))
    ->withNetworkId('mainnet0')
    ->withNonce('nonce-string')
    ->make();
```
The `withExecutePayload` or the `withContinuePayload` options are always required to create a `Command` object, but all others are optional.

### Signing Commands
After creating a command, you can sign it using any number of key pairs. To do this, first, create a `KeyPairCollection` from the key pairs you have. 
These key pairs should correspond to the signers you added to your account.
```php
$kpc = new KeyPairCollection($keypair);
```

Now using these key pairs, we can sign the previously created command
```php
$signedCommand = CommandSigner::sign($command, $kpc);
```
This returns a new instance of a `SignedCommand`

### Constructing commands from a string
Instead of signing the command in the backend, a command might be signed elsewhere (user wallet). 
A signed command can be reconstructed from a valid Pact command string using:
```php
$signedCommand = SignedCommandMapper::fromString($commandString)
```
A signed command can also be cast to a string or an array using
```php
$commandString = SignedCommandMapper::toString($signedCommand);
$commandArray = SignedCommandMapper::toArray($signedCommand);
```

### Using the Client
Now we have figured out how to create commands and sign them, it's time to use them to make calls to the Pact API.

First, create a new API client:

```php
$client = new \Kadena\Client('http://localhost:8888'); // or whatever local config you have
```
The client has a few methods available, 
see the [Pact API docs](https://api.chainweb.com/openapi/pact.html#tag/endpoint-local) for more information on the different use-cases
and expected responses.
#### local
Takes a single `SignedCommand` as a parameter and returns a `ResponseInterface`.
```php
$local = $client->local($signedCommand);
```
#### send
Takes a multiple `SignedCommand` wrapped in a `SignedCommandCollection` as a parameter and returns a `RequestKeyCollection`.
```php
$send = $client->send(new SignedCommandCollection($signedCommand));
```
#### listen
Takes a single `RequestKey` as a parameter and returns a `ResponseInterface`.
```php
$requestKey = $send->first(); // Get a RequestKey from the send response above
$listen = $client->single($requestKey);
```
#### poll
Takes a `RequestKeyCollection` as a parameter and returns a `ResponseInterface`.
```php
$requestKeyCollection = $send; // The send() method above returned a RequestKeyCollection
$poll = $client->poll($requestKeyCollection);
```
#### spv
Takes a `RequestKey` and a `string $targetChainId` as parameters and returns a `string`.
```php
$spv = $client->spv($requestKey, '2');
```


## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
./vendor/bin/phpunit
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security-related issues, please send an [email](mailto:hergen.dillema@gmail.com) instead of using the issue tracker.

## Credits

- [Hergen Dillema][link-author]
- [All Contributors][link-contributors]

## License

MIT. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/kadena-php/client.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/kadena-php/client.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/kadena-php/client
[link-downloads]: https://packagist.org/packages/kadena-php/client
[link-author]: https://github.com/hergend
[link-contributors]: ../../contributors
