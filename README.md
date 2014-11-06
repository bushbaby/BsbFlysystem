# BsbFlysystem

A simple Zend Framework 2 module that bridges the Flysystem filesystem.

[![Latest Stable Version](https://poser.pugx.org/bushbaby/flysystem/v/stable.svg)](https://packagist.org/packages/bushbaby/flysystem) [![Total Downloads](https://poser.pugx.org/bushbaby/flysystem/downloads.svg)](https://packagist.org/packages/bushbaby/flysystem) [![Latest Unstable Version](https://poser.pugx.org/bushbaby/flysystem/v/unstable.svg)](https://packagist.org/packages/bushbaby/flysystem) [![License](https://poser.pugx.org/bushbaby/flysystem/license.svg)](https://packagist.org/packages/bushbaby/flysystem)


[![Build Status](https://travis-ci.org/bushbaby/BsbFlysystem.svg?branch=master)](https://travis-ci.org/bushbaby/BsbFlysystem)
[![Code Coverage](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/badges/build.png?b=master)](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/build-status/master)
[![Dependency Status](https://www.versioneye.com/user/projects/545a9e49114a5db6d5000006/badge.svg?style=flat)](https://www.versioneye.com/user/projects/545a9e49114a5db6d5000006)

Provides a way to configure the various filesystem adapters provided by thephpleague's 'Flysystem'. And allows to retrieve fully configured filesystems by name from the ServiceLocator. Whether the defined filesystems are local- or dropbox filesystems becomes a configuration detail.

## Installation

```sh
php composer.phar require "bushbaby/flysystem:~0.1"
```

Then add `BsbFlysystem` to the `config/application.config.php` modules list.

Copy the `config/bsb_flysystem.global.php.dist` to the `config/autoload` directory to jump start configuration. 

## Requirements

- \>=PHP5.4
- \>=ZF2.2.0

## Configuration

All configuration regarding BsbFlysystem life in the `bsb_flysystem` config key.

The configuration consists of the following base elements;

- *Adapters* are consumed by a Filesystem.
- *Caches* are consumed by a Filesystem.
- *Filesystems* filesystem are consumed in userland.

### Adapters

To configure an adapter you add a key to `bsb_flysystem->adapters` with a associative array containing the following options;

- type    \<string\>  Type of adapter
- shared  \<boolean\> (optional) Defines the shared option of a [ZF2 service](http://framework.zend.com/manual/2.0/en/modules/zend.service-manager.quick-start.html#using-configuration).
- options \<array\> Options specific per adapter (see [flysystem](http://flysystem.thephpleague.com) or config/bsb_flysystem.global.php.dist)


example: a local adapter pointing to ./data/files

```php
<?php
    'bsb_flysystem' => [
        'adapters' => [
            'local_files' => [
                'type' => 'local',
                'options' => [
                    'root' => './data/files'
                ],
            ],
        ],
    ],
```

### Filesystems

Configure a filesystems by adding to `bsb_flysystem->filesystems`. Each filesystem may containing the following options;

- adapter \<string\>  Name of adapter service.
- cache   \<string\> (optional) If defined a name of a cache service. Defaults to false.
- eventable \<boolean\> When true returns an EventableFilesystem instance. (see [flysystem](http://flysystem.thephpleague.com).

example: Filesystem called 'files' with the previously defined 'local_files' adapter.

```php
<?php
    'bsb_flysystem' => [
        'filesystems' => [
            'files' => [
    	        'adapter' => 'local_files',
    	        'cache' => false,
    	        'eventable => false,
            ],
        ],
    ],
```

### Caches

\- **Not yet implemented** -

Configure a caches by adding to `bsb_flysystem->caches`. Each cache may containing the following options;

example: Cache called 'memcached'.

```php
<?php
    'bsb_flysystem' => [
        'caches' => [
            'memcached' => [
            ],
        ],
    ],
```

#### AdapterManager

The AdapterManager is automaticly configured, However it is possible to tweak its configuration via `bsb_flysystem->adapter_manager`. 

Inparticular the lazy_services may be usefull if you use the Rackspace Adapter. BsbFlystem lazy load that adapter so it will not create a connection until you actually use adapter. This done with help from [ProxyManager](https://github.com/Ocramius/ProxyManager). As ZF2 also uses this libary we take advantage of the 'lazy_services' configuration that may be available in your application. The Rackspace adapter merges the ZF2 lazy_services config key with the adapter_manager lazy_services config allowing control over how the ProxyManager handles it's thing.

```php
<?php
    'bsb_flysystem' => [
        'adapter_manager' => [
            'services'      => [
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

By default BsbFlysystem provides one pre-configured filesystem. This is a local filesystem (uncached) and exposes the data directory of a default ZF2 application.

Both the filesystems and adapters are ZF2 plugin managers and stored within the global service manager.

### Filesystem Manager

In its simplest form this is how we would retrieve a filesystem. We get the filesystem service from the main service manager and fetch from that a filesystem instance. 

example: Fetch a 'default' filesystem. In this case a 'local' filesystem with a root of 'data'.

```php
<?php
    $filesystem = $serviceLocator->get('BsbFlysystemManager')->get('default');
    $contents   = $filesystem->read('file.txt');
```

If we at some point decide we need to store these files on a different system. Rackspace for example, we simply reconfigure the named filesystem service to use a different named adapter service. No need to change the userland implementation.

### Adapter Manager

Direct access to the Adapter service is possible by via the `BsbFlysystemAdapterManager` service registered in the main service locator. This is useful to setup `Mount` Filesystems or to use runtime configuration. See the advanced section below.

```php
<?php
    $adapter    = $serviceLocator->get('BsbFlysystemAdapterManager')->get('local_data');
    $filesystem = new Filesystem($adapter);
    $contents   = $filesystem->read('file.txt');
```

## Provided Factories

I have tried to provide factories (and tests) for each of the adapters that come with the Flysystem. Each come with there own setof required and optional options. I refer to the Flysystem documentation for more inforation.

### Adapters

- Aws3S
- Copy
- Dropbox
- Ftp
- Local
  - BsbFlysystem is preconfigured with an adapter named 'local_data' to expose the ./data directory of a ZF2 application
- Null
- Rackspace
  - the ObjectStore Container must exist before usage
  - Won't connect until actual usage by Filesystem (thanks to [ProxyManager](https://github.com/Ocramius/ProxyManager)) and uses the same lazy loading configuration ZF2 provides.
- Replicate
- Sftp
- WebDav
- Zip

### Cache

\- **not yet implemented** -

### Filesystems

There is one FilesystemFactory which creates a Filesystem of EventableFilesystem based on the configuration

## Advanced Usage

### Shared option and createOptions

A feature of ZF2 service managers is the ability to create an instance of a service each time you request it from the service manager (shared vs unshared). As a convienence this can be easily accomplished by setting 'shared' to false/true. Together with 'createOptions' that can be provided to the get method of a service manager this is useful to override option values. 

Consider the following configuration; Retrieve multiple configured dropbox filesystems based on stored accessTokens retrieved at runtime.

```php
<?php
    'adapters' => [
        'dropbox_user' => [
            'type' => 'dropbox',
            'shared' => false,
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
<?php
    $accessTokens = [...];
    foreach ($accessTokens as $accessToken) {
        $adapter    = $serviceLocator->get('BsbFlysystemAdapterManager')
                                     ->get('dropbox_user', [
                                         'access_token' => $accessToken
                                     ]);

        $filesystem = new Filesystem($adapter);
        $filesystem->put('TOS.txt', 'hi!');
    }
```

Using the same createOptions feature but now directly from the Filesystem Manager. Note: the adapter_options key which are passed to the Adapter Manager by the FilesystemFactory.

```php
<?php
    $accessTokens = [...];
    foreach ($accessTokens as $accessToken) {
        $filesystem    = $serviceLocator->get('BsbFlysystemManager')
                                        ->get('dropbox_user', [
                                            'adapter_options' => [
                                               'access_token' => $accessToken
                                            ]
                                        ]);
 
        $filesystem = new Filesystem($adapter);
        $filesystem->put('TOS.txt', 'hi!');
    }
```

### Mount Manager

```php
<?php
    $sourceFilesystem    = $serviceLocator->get('BsbFlysystemManager')->get('default'); // local adapter ./data
    $targetFilesystem    = $serviceLocator->get('BsbFlysystemManager')->get('archive'); // eg. zip archive

    $manager = new League\Flysystem\MountManager(array(
        'source' => $sourceFilesystem,
        'target' => $targetFilesystem,
    ));

    $contents = $manager->listContents('source://some_directory', true);
    foreach ($contents as $entry) {
	    $manager->put('target://'.$entry['path'], $manager->read('source://'.$entry['path']));
    }
```

