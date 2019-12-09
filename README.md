# BsbFlysystem

A simple Zend Framework 2 module that bridges the Flysystem filesystem.

[![Latest Stable Version](https://poser.pugx.org/bushbaby/flysystem/v/stable)](https://packagist.org/packages/bushbaby/flysystem) 
[![Total Downloads](https://poser.pugx.org/bushbaby/flysystem/downloads)](https://packagist.org/packages/bushbaby/flysystem) 
[![Latest Unstable Version](https://poser.pugx.org/bushbaby/flysystem/v/unstable)](https://packagist.org/packages/bushbaby/flysystem) 
[![License](https://poser.pugx.org/bushbaby/flysystem/license)](https://packagist.org/packages/bushbaby/flysystem)

[![Build Status](https://travis-ci.org/bushbaby/BsbFlysystem.svg?branch=master)](https://travis-ci.org/bushbaby/BsbFlysystem)
[![Code Coverage](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/bushbaby/BsbFlysystem/?branch=master)

Provides a way to configure the various filesystem adapters provided by thephpleague's 'Flysystem'. And allows to retrieve fully configured filesystems by name from the ServiceLocator. Whether the defined filesystems are local- or dropbox filesystems becomes a configuration detail.

## Installation

```
php composer.phar require "bushbaby/flysystem:^5.0"
```

Then add `BsbFlysystem` to the `config/application.config.php` modules list.

Copy the `config/bsb_flysystem.local.php.dist` to the `config/autoload` directory to jump start configuration. 

## Requirements

- \>=PHP7.2
- \>=ZF2.7

## Configuration

Configuration regarding BsbFlysystem lives in the top-level configuration key `bsb_flysystem`.

The configuration consists of the following base elements;

- *Adapters* are consumed by a Filesystem.
- *Filesystems* filesystem are consumed in userland.
- *Adapter Map* is used to overload the default adapter list.

### Adapters

To configure an adapter you add a key to `bsb_flysystem->adapters` with a associative array containing the following options;

- type    \<string\>  Type of adapter
- shared  \<boolean\> (optional) Defines the shared option of a [ZF2 service](http://framework.zend.com/manual/2.0/en/modules/zend.service-manager.quick-start.html#using-configuration).
- options \<array\> Options specific per adapter (see [flysystem](http://flysystem.thephpleague.com) or config/bsb_flysystem.local.php.dist)


example: a local adapter pointing to ./data/files

```
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
- cache   \<string\> (optional) If defined as string it should be a name of a service present in the main service locator. Defaults to false.
- eventable \<boolean\> When true returns an EventableFilesystem instance. (see [flysystem](http://flysystem.thephpleague.com)).
- plugins \<array\> List of FQCN to the plugin you wish to register for this filesystem

example: Filesystem called 'files' with the previously defined 'local_files' adapter and the 'listFiles' plugin registered.

```
'bsb_flysystem' => [
    'filesystems' => [
        'files' => [
	        'adapter' => 'local_files',
	        'cache' => false,
	        'eventable' => false,
	        'plugins' => [
	        	'League\Flysystem\Plugin\ListFiles',
    		],
        ],
    ],
],
```


### Adapter Map

By default, BsbFlysystem provides a [list of adapters](src/Service/Factory/AdapterManagerFactory.php#L18-35) that are ready to used.

If you need to add a custom adapter you are able to by registering it onto the `adapter_map` key.

example : Add a custom Adapter called 'customAdapter' using an invokable class 'Custom\Adapter'

```
'bsb_flysystem' => [
    'adapters' => [
        'named_adapter' => [
            'type'   => 'customAdapter',
            'shared' => true,
        ]
    ],
    'adapter_map' => [
        'invokables' => [
            'customAdapter' => 'Custom\Adapter'
        ]
    ]
];
```

#### Caching

No cache factories are provided by BsbFlysystem. You should write them yourself and register them in main service manager. You can use these by setting the cache option to the name of the service.

```
'bsb_flysystem' => [
    'filesystems' => [
        'files' => [
	        'cache' => 'My/Service/Cache',
        ],
    ],
],
'service_manager' => [
    'factories' => [
    	'My/Service/Cache' => 'My/Service/CacheFactory'
    ]
]
```

BsbFilesystem is able to automaticly wrap a ZF2 caching service in a in such way that a Flysystem instance is able to consume it.

This means that BsbFlysystem can work with both flysystem caches (implementing `League\Flysystem\Cached\CacheInterface`) and ZF2 caches (implementing `Zend\Cache\Storage\StorageInterface`).

example: caching options as are common in a ZF2 application

```
'bsb_flysystem' => [
    'filesystems' => [
        'files' => [
	        'cache' => 'Cache\BsbFlystem\Memory',
        ],
    ],
],
'caches' => [
    'Cache\BsbFlystem\Memory' => [
        'adapter' => [
            'name'    => 'memory',
            'options' => [
                'ttl'       => 300,
            ],
        ],
    ],
],
'service_manager' => [
    'abstract_factories' => [
    	\Zend\Cache\Service\StorageCacheAbstractServiceFactory::class
    ],
],
```
 
Further reading in ZF2 [documentation](http://framework.zend.com/manual/current/en/modules/zend.mvc.services.html#zend-cache-service-storagecacheabstractservicefactory).

#### AdapterManager

The AdapterManager is automaticly configured, However it is possible to tweak its configuration via `bsb_flysystem->adapter_manager`. 

In particular the lazy_services configuration key may be useful if you use the Rackspace Adapter. BsbFlysystem loads that adapter 'lazily'. A connection is only established until you actually use the adapter. This done with help from [ProxyManager](https://github.com/Ocramius/ProxyManager). As ZF2 also uses this libary we take advantage of the 'lazy_services' configuration that may be available in your application. The Rackspace adapter merges the ZF2 lazy_services config key with the adapter_manager lazy_services config allowing control over how the ProxyManager handles it's thing.

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

By default BsbFlysystem provides one pre-configured filesystem. This is a local filesystem (uncached) and exposes the data directory of a default ZF2 application.

Both the filesystems and adapters are ZF2 plugin managers and stored within the global service manager.

### Filesystem Manager

In its simplest form this is how we would retrieve a filesystem. We get the filesystem service from the main service manager and fetch from that a filesystem instance. 

example: Fetch a 'default' filesystem. In this case a 'local' filesystem with a root of 'data'.

```
$filesystem = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)->get('default');
$contents   = $filesystem->read('file.txt');
```

If we at some point decide we need to store these files on a different system. Rackspace for example, we simply reconfigure the named filesystem service to use a different named adapter service. No need to change the userland implementation.

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

- Azure
- Aws3S (v3 only)
- Dropbox
- Ftp
- Ftpd
- GoogleCloudDrive
- Local
  - BsbFlysystem is preconfigured with an adapter named 'local_data' to expose the ./data directory of a ZF2 application.
- Null
- Rackspace
  - the ObjectStore Container must exist before usage
  - Won't connect until actual usage by Filesystem (thanks to [ProxyManager](https://github.com/Ocramius/ProxyManager)) and uses the same lazy loading configuration ZF2 provides.
- Replicate
- Sftp
- VFS
- WebDAV
- ZipArchive

A note about the AwsS3 adapter; There are two versions of the AwsS3 sdk and only one can be installed at the same time. Therefore the Aws3S and Aws3Sv2 adapters are not required as dev-dependancies and are (at the moment) not unit tested.

### Filesystems

There is one FilesystemFactory which creates a Filesystem or EventableFilesystem based on the configuration

## Advanced Usage

### Shared option and createOptions

A feature of ZF2 service managers is the ability to create an instance of a service each time you request it from the service manager (shared vs unshared). As a convienence this can be easily accomplished by setting 'shared' to false/true. Together with 'createOptions' that can be provided to the get method of a service manager this is useful to override option values. 

Consider the following configuration; Retrieve multiple configured dropbox filesystems based on stored accessTokens retrieved at runtime.

```
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

```
$accessTokens = [...];
foreach ($accessTokens as $accessToken) {
    $adapter    = $serviceLocator->get(\BsbFlysystem\Service\AdapterManager::class)
                                 ->get('dropbox_user', [
                                     'access_token' => $accessToken
                                 ]);

    $filesystem = new Filesystem($adapter);
    $filesystem->put('TOS.txt', 'hi!');
}
```

Using the same createOptions feature but now directly from the Filesystem Manager. Notice the adapter_options key which are passed to the Adapter Manager by the FilesystemFactory.

```
$accessTokens = [...];
foreach ($accessTokens as $accessToken) {
    $filesystem    = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)
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

```
$sourceFilesystem    = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)->get('default'); // local adapter ./data
$targetFilesystem    = $serviceLocator->get(\BsbFlysystem\Service\FilesystemManager::class)->get('archive'); // eg. zip archive

$manager = new League\Flysystem\MountManager(array(
    'source' => $sourceFilesystem,
    'target' => $targetFilesystem,
));

$contents = $manager->listContents('source://some_directory', true);
foreach ($contents as $entry) {
    $manager->put('target://'.$entry['path'], $manager->read('source://'.$entry['path']));
}
```

### RenameUpload filter

@since 1.3.0

`BsbFlysystem\Filter\File\RenameUpload` can be used to rename or move an uploaded file to a Flysystem filesystem.

This class takes an `filesystem` constructor option which must implement `League\Flysystem\FilesystemInterface`.

The `BsbFlysystem\Filter\File\RenameUpload` extends `Zend\Filter\File\RenameUpload` class so I refer to the Flysystem [documentation](http://framework.zend.com/manual/current/en/modules/zend.filter.file.rename-upload.html#zend-filter-file-rename-upload) for more information.

```
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
