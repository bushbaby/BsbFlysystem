# BsbFlysystem

A simple Laminas module that bridges the Flysystem filesystem.

[![Latest Stable Version](http://poser.pugx.org/bushbaby/flysystem/v)](https://packagist.org/packages/bushbaby/flysystem) 
[![Total Downloads](http://poser.pugx.org/bushbaby/flysystem/downloads)](https://packagist.org/packages/bushbaby/flysystem) 
[![License](http://poser.pugx.org/bushbaby/flysystem/license)](https://packagist.org/packages/bushbaby/flysystem) 
[![PHP Version Require](http://poser.pugx.org/bushbaby/flysystem/require/php)](https://packagist.org/packages/bushbaby/flysystem) 
[![Test](https://github.com/bushbaby/BsbFlysystem/actions/workflows/test.yml/badge.svg)](https://github.com/bushbaby/BsbFlysystem/actions/workflows/test.yml)
[![Coverage Status](https://coveralls.io/repos/github/bushbaby/BsbFlysystem/badge.svg?branch=master)](https://coveralls.io/github/bushbaby/BsbFlysystem?branch=master)

Provides a way to configure the various filesystem adapters provided by thephpleague's 'Flysystem'. And allows to retrieve fully configured filesystems by name from the ServiceLocator. Whether the defined filesystems are local- or dropbox filesystems becomes a configuration detail.

## Installation

```
composer require bushbaby/flysystem
```

Then add `BsbFlysystem` to the `config/application.config.php` modules list.

Copy the `config/bsb_flysystem.local.php.dist` to the `config/autoload` directory to jump start configuration. 

## Deprecations

- dropped support for [FlySystem](https://flysystem.thephpleague.com/docs/) v1 and v2
- dropped support for [EventableFilesystem](https://github.com/thephpleague/flysystem-eventable-filesystem)
- dropped support for [CachedAdapter](https://github.com/thephpleague/flysystem-cached-adapter)
- dropped support for [Laminas Service Manager v2](https://docs.laminas.dev/laminas-servicemanager/migration)
- dropped support for [Null Adapter](https://github.com/thephpleague/flysystem/issues/1303)
- dropped support for [VFS Adapter](https://github.com/thephpleague/flysystem-eventable-filesystem)
- dropped support for [RackSpace Adapter](https://github.com/thephpleague/flysystem-rackspace)
- dropped support for [Plugin System](https://flysystem.thephpleague.com/docs/what-is-new/)

## Migrating to v8

Configuration changes

- For the adapter configuration the 'type' key has been renamed to 'factory'. Each adapter is now referenced by its FQCN in a 'factory' key.
- The 'options' is now passed to the adapter factory as an named arguments array. Look at individual adapter constructor which opions are available. Some options - such as 'mimeTypeDetector' may be a valid service names. The adapter will pull an instance from the service manager.
- The 'shared' option has been removed. Both the filesystem- and adapter service are plugin managers. Services pulled from a Laminas Plugin Manager with additional creation options are using the build method of the service manager and are thus not shared.
- The 'adapter_map' has been removed. The adapter configuration is now done in the 'adapters' key. The 'adapter_map' was used to overload the default adapter list. This is no longer needed as the default adapter list is now empty.
- And ofcourse look at the api changes from Flysystem [Upgrade from 1.x
](https://flysystem.thephpleague.com/docs/upgrade-from-1.x/) and [new in Flysystem V2 & V3](https://flysystem.thephpleague.com/docs/what-is-new/)

## Configuration

Configuration regarding BsbFlysystem lives in the top-level configuration key `bsb_flysystem`.

The configuration consists of the following base elements;

- *Adapters* are consumed by a Filesystem.
- *Filesystems* filesystem are consumed in userland.

### Adapters

To configure an adapter you add a key to `bsb_flysystem->adapters` with a associative array containing the following options;

- factory \<string\> The FQCN of a factory used to create an adapter.
- options \<array\> named arguments passed to each adapter constructor (see [flysystem](http://flysystem.thephpleague.com) or peek into config/bsb_flysystem.local.php.dist)
- options.prefix \<string\> (optional) Any filesystem adapter can be scoped down to a prefixed path using [Path Prefixing](https://flysystem.thephpleague.com/docs/adapter/path-prefixing/). 
- options.readonly \<boolean\> (optional) Any filesystem adapter can be made read-only [Read only adapter](https://flysystem.thephpleague.com/docs/adapter/read-only/). 

example: a readonly local adapter pointing to ./data/files

```
'bsb_flysystem' => [
    'adapters' => [
        'local_files' => [
            'factory' => BsbFlysystem\Adapter\Factory\LocalAdapterFactory::class,
            'options' => [
                'location' => './data/files',
                'readonly' => true,
            ],
        ],
    ],
],
```

### Filesystems

Configure a filesystem by adding to `bsb_flysystem->filesystems`. Each filesystem may containing the following options;

- adapter \<string\>  Name of adapter service.

```
'bsb_flysystem' => [
    'filesystems' => [
        'files' => [
	        'adapter' => 'local_files',
        ],
    ],
],
```

It is possible to override options given to the adapter factory via the 'adapter_options' key. This is useful if you want to use the same adapter for multiple filesystems but with different options. Typically you would do that when you manually pull a filesystem or adapter from the a plugin manager.

#### AdapterManager

The AdapterManager is automaticly configured, however it is possible to tweak its configuration via `bsb_flysystem->adapter_manager`. 

In particular the lazy_services configuration key may be useful if you use the Rackspace Adapter. BsbFlysystem loads that adapter 'lazily'. A connection is only established until you actually use the adapter. This done with help from [ProxyManager](https://github.com/Ocramius/ProxyManager). As Laminas also uses this libary we take advantage of the 'lazy_services' configuration that may be available in your application. The Rackspace adapter merges the Laminas lazy_services config key with the adapter_manager lazy_services config allowing control over how the ProxyManager handles it's thing.

```
'bsb_flysystem' => [
    'adapter_manager' => [
        'config'      => [
        ],
        'lazy_services' => [
            // directory where proxy classes will be written - default to system_get_tmp_dir()
            // 'proxies_target_dir'    => 'data/cache',
            // namespace of the generated proxies, default to "ProxyManagerGeneratedProxy"
            // 'proxies_namespace'     => null,
            // whether the generated proxy classes should be written to disk
            // 'write_proxy_files'     => false,
        ],
    ],
],
```

## Usage

By default BsbFlysystem provides one pre-configured filesystem. This is a local filesystem and exposes the data directory of a default Laminas application. This directory is configured to have 'lazyRootCreation'. 

Both the filesystem- and adapter services are Laminas Plugin Managers and stored within the global service manager. Aliases are registered for both; BsbFlysystemManager and BsbFlysystemAdapterManager.

### Filesystem Manager

In its simplest form this is how we would retrieve a filesystem. We get the filesystem service from the main service manager and fetch from that a filesystem instance. 

example: Fetch a 'default' filesystem. In this case a 'local' filesystem with a root of 'data'.

```
$filesystem = $serviceLocator->get('BsbFlysystemManager')->get('default');
$contents   = $filesystem->read('file.txt');
```

If at some point you decide files need to be stored on a different system you simply reconfigure the named filesystem service to use a different adapter service. No need to change the userland implementation.

### Adapter Manager

Direct access to the Adapter service is possible by via the `BsbFlysystemAdapterManager` service registered in the main service locator. This is useful to setup `Mount` Filesystems or to use runtime configuration. See the advanced section below.

```
$adapter    = $serviceLocator->get(\BsbFlysystem\Service\AdapterManager::class)->get('local_data');
$filesystem = new Filesystem($adapter);
$contents   = $filesystem->read('file.txt');
```
## Provided Factories

I have tried to provide factories (and tests) for each of the adapters that come with the Flysystem. Each come with there own set of required and optional options. I refer to the Flysystem documentation for more information.

### Adapters

- Aws3Sv3
- AzureBlobStorage
- Dropbox
- Ftp
- GoogleCloudStorage
- InMemory
- Local
  - BsbFlysystem is preconfigured with an adapter named 'local_data' to expose the ./data directory of a Laminas application.
- Replicate
- Sftp
- WebDAV
- ZipArchive

A note about the AwsS3 adapter; There are two versions of the AwsS3 sdk and only one can be installed at the same time. Therefore the Aws3S and Aws3Sv2 adapters are not required as dev-dependancies and are (at the moment) not unit tested.

### Filesystems

There is one FilesystemFactory which creates a Filesystem based on the configuration.

## Advanced Usage

### Shared option and createOptions

A feature of Laminas service managers is the ability to create an instance of a service each time you request it from the service manager (shared vs unshared). As a convienence this can be easily accomplished by setting 'shared' to false/true. Together with 'createOptions' that can be provided to the get method of a service manager this is useful to override option values. 

Consider the following configuration; Retrieve multiple configured dropbox filesystems based on stored accessTokens retrieved at runtime.

```php
'adapters' => [
    'dropbox_user' => [
        'type' => 'dropbox',
        'options' => [
            'client_identifier' => 'app_id',
            'access_token'      => 'xxxxx',
        ],
    ],
],
'filesystems' => [
    'dropbox_user' => [
        'shared' => false,
        'adapter' => 'dropbox_user'
    ],
],
```

```php
$accessTokens = [...];
foreach ($accessTokens as $accessToken) {
    $adapter = $serviceLocator->get(\BsbFlysystem\Service\AdapterManager::class)
                              ->get('dropbox_user', ['access_token' => $accessToken]);

    $filesystem = new Filesystem($adapter);
    $filesystem->put('TOS.txt', 'hi!');
}
```
Using the same createOptions feature but now directly from the Filesystem Manager. Notice the adapter_options key which are passed to the Adapter Manager by the FilesystemFactory.

```php
$accessTokens = [...];
foreach ($accessTokens as $accessToken) {
    $filesystem  = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)
                                  ->get('dropbox_user', [
                                      'adapter_options' => ['access_token' => $accessToken]
                                  ]);

    $filesystem = new Filesystem($adapter);
    $filesystem->put('TOS.txt', 'hi!');
}
```

### Mount Manager

```php
$sourceFilesystem = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)->get('default'); // local adapter ./data
$targetFilesystem = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)->get('archive'); // eg. zip archive

$manager = new League\Flysystem\MountManager(array(
    'source' => $sourceFilesystem,
    'target' => $targetFilesystem,
));

$contents = $manager->listContents('source://some_directory', true);
foreach ($contents as $entry) {
    $manager->write('target://'.$entry->path(), $manager->read('source://'.$entry->path()));
}
```

### RenameUpload filter

@since 1.3.0

`BsbFlysystem\Filter\File\RenameUpload` can be used to rename or move an uploaded file to a Flysystem filesystem.

This class takes an `filesystem` constructor option which must implement `League\Flysystem\Filesystem`.

The `BsbFlysystem\Filter\File\RenameUpload` extends `Laminas\Filter\File\RenameUpload` class so I refer to the Laminas [documentation](https://docs.laminas.dev/laminas-filter/file/#renameupload) for more information.

```php
$request = new Request();
$files   = $request->getFiles();
// i.e. $files['my-upload']['tmp_name'] === '/tmp/php5Wx0aJ'
// i.e. $files['my-upload']['name'] === 'myfile.txt'

// get a filesystem from the BsbFlysystemManager (or construct one manually)
$filesystem = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)->get('default');

$filter = new \BsbFlysystem\Filter\File\RenameUpload([
    'target' => 'path/to/file.txt',
    'filesystem' => $filesystem
]);

$filter->filter($files['my-upload']);
// or
$filter->filter('path/to/local/file.txt');

// File has been renamed and moved through $filesystem with key 'path/to/file.txt'
```
