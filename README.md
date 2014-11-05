# BsbFlysystem

A simple Zend Framework 2 module that bridges the Flysystem filesystem.

[![Latest Stable Version](https://poser.pugx.org/bushbaby/flysystem/v/stable.svg)](https://packagist.org/packages/bushbaby/flysystem) [![Total Downloads](https://poser.pugx.org/bushbaby/flysystem/downloads.svg)](https://packagist.org/packages/bushbaby/flysystem) [![Latest Unstable Version](https://poser.pugx.org/bushbaby/flysystem/v/unstable.svg)](https://packagist.org/packages/bushbaby/flysystem) [![License](https://poser.pugx.org/bushbaby/flysystem/license.svg)](https://packagist.org/packages/bushbaby/flysystem)

Provides a way to configure the various filesystem adapters provided by thephpleague's 'Flysystem'. Visit [flysystem](http://flysystem.thephpleague.com) for detailed usage of the Flysystem library.

Note: WIP do not use in production!

This module allows to retrieve fully configured filesystems by name from the ServiceLocator. These filesystems will be fully configured. This allows your code be agnostic the configuration of adpaters and filesystems. Whether the filesystem is a local filesystem or remote dropbox account, whether you enable caching or use the replicate adapter becomes a configuration detail.

## Installation

```sh
php composer.phar require "bushbaby/flysystem:~0.1"
```

Then add `BsbFlysystem` to the `config/application.config.php` modules list.

Copy the `config/bsb_flysystem.global.php.dist` to the `config/autoload` directory to jump start configuration. 

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

```
<?php
    'bsb_flysystem' => array(
        'adapters' => array(
            'local_files' => array(
                'type' => 'local',
                'options' => array(
                    'root' => './data/files'
                ),
            ),
        ),
    ),
```

### Filesystems

Configure a filesystems by adding to `bsb_flysystem->filesystems`. Each filesystem may containing the following options;

- adapter \<string\>  Name of adapter service.
- cache   \<string\> (optional) If defined a name of a cache service. Defaults to false.
- eventable \<boolean\> When true returns an EventableFGilesystem instance. (see [flysystem](http://flysystem.thephpleague.com).

example: Filesystem called 'files' with the previously defined 'local_files' adapter.

```php
    'bsb_flysystem' => array(
        'filesystem' => array(
            'files' => array(
    	        'adapter' => 'local_files',
    	        'cache' => false,
    	        'eventable => false,
            )
        )
    ),
```

### Caches

- **Not yet implemented** -

Configure a caches by adding to `bsb_flysystem->caches`. Each cache may containing the following options;

example: Cache called 'memcached'.

```php
    'bsb_flysystem' => array(
        'caches' => array(
            'memcached' => array(

            )
        )
    ),
```


## Usage

By default BsbFlysystem provides one pre-configured filesystem. This is a local filesystem (uncached) and exposes the data directory of a default ZF2 application.

Both the filesystems and adapters are ZF2 plugin managers and stored within the global service manager.

### Filesystem Manager

In its simplest form this is how we would retrieve a filesystem. We get the filesystem service from the main service manager and fetch from that a filesystem instance. 

example: Fetch a 'default' filesystem. In this case a 'local' filesystem with a root of 'data'.

```
$filesystem = $serviceLocator->get('BsbFlysystemManager')->get('default');
$contents   = $filesystem->read('file.txt');
```

If we at some point decide we need to store these files on a different system. Rackspace for example, we simply reconfigure the named filesystem service to use a different named adapter service. No need to change the userland implementation.

### Adapter Manager

Direct access to the Adapter service is possible by via the `BsbFlysystemAdapterManager` service registered in the main service locator. This is useful to setup `Mount` Filesystems or to use runtime configuration. See the advanced section below.

```
$adapter    = $serviceLocator->get('BsbFlysystemAdapterManager')->get('local.data');
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
- Null
- Rackspace
  - the ObjectStore Container must exist before usage
  - Won't connect until actual usage by Filesystem (thanks to [ProxyManager](https://github.com/Ocramius/ProxyManager)) and uses the same lazy loading configuration ZF2 provides.
- Replicate
- Sftp
- WebDav
- Zip

### Cache



## Advanced Usage

A feature of ZF2 service managers is the ability to create an instance of a service each time you request it from the service manager (shared vs unshared). For adapters this can be easily accomplished by setting 'shared' to false/true. This in combination with the create options that can be provided to the get method of the service manager is useful to override certain configuration keys. 

Consider the following configuration.

```
       'adapters' => array(
           'dropbox_user' => array(
                'type' => 'dropbox',
                'shared' => false,
                'options' => array(
                    'client_identifier' => 'app_id',
                    'access_token'      => 'xxxxx',
                ),
            ),
        ),
        'filesystems' => array(
        	'dropbox_user' => array(
        		'adapter' => 'dropbox_user'
        	)
        ),
```

```
$adapter    = $serviceLocator->get('BsbFlysystemAdapterManager')
                             ->get('dropbox_user', ['access_token' => $accessToken]);
$filesystem = new Filesystem($adapter);
```


