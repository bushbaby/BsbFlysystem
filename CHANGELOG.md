# CHANGELOG

v4.0.0

- added support for zend-expressive
- removed zendframework/zend-modulemanager dependency
- dropped support for `php 5`
- dropped support for `aws s3 version 2` adapter
- added support for zend-expressive
- changed cs tooling

v3.0.0

- adds a replacement for the deprecated `league/flysystem-dropbox` adapter via [`spatie/flysystem-dropbox`](https://github.com/spatie/flysystem-dropbox) which internally uses dropbox api version 2.
- remove support for `league/flysystem-copy` as that service is EOL

v2.0.0

- adds aim option for aws-sdk-v3 adapter

v2.0.0-RC1

- adds compatibility with zend-servicemanager 3.0 and therefore zend-servicemanager 2.7.3 is the lowest version you can use
- support for php5.4 was dropped and for php7.0 added
- Possibly BC: where previously \UnexpectedValueException was thrown a BsbFlysystem\Exception\UnexpectedValueException is thrown
- Possibly BC: where previously \RuntimeException was thrown a BsbFlysystem\Exception\RuntimeException is thrown
- Possibly BC: adapter factories don't implement FactoryInterface as they extends AbstractAdapterFactory which does that
- Possibly BC: the abstract filesystem factory has been replaced by a regular factory. For each configured filesystem an entry is now dynmicly added to the filesystem plugin manager. This way the shared option for filesystems can be configured correctly (in sm2.7).
- marked azure test as skipped; currently the azure adapter won't work with microsoft/windowsazure to version 0.4.2. [see](https://github.com/thephpleague/flysystem-azure/pull/16)

v1.4.1

- factories no longer depends on initializers which are a deprecated function of service manager
- some composer restrictions where added on the lowest possible package versions 
- SSL for PEAR repositories as composer does not allow non secure connection by default anymore

v1.4.0

- adds Guzzle request options support for Aws S3
- adds an adapter for the VFS adapter

v1.3.0

- adds an FileUploadFilter capable of persisting uploaded files directly to an Flysystem filesystem

v1.2.2

- adds an adapter for version 3 of the AwsS3 SDK
- removes dev requirement and disables unittests for AwsS3 (v2). We cannot force a dependancy as v2 cannot be used together with and v3.
- moves `bsb_flysystem.global.php.dist` to `bsb_flysystem.local.php.dist` as these might contain credentials and should not be placed under version control

v1.2.1

- composer now installs the needed zf2 packages instead of the full framework

v1.2.0

- adds the ability to overload (add a custom one or modify an existing one) the provided adapters

v1.1.0

- adds adapter for Azure

v1.0.1

- SftpAdapter 'password' is not required option when 'privateKey' is set

v1.0.0

- none

v1.0.0-dev

- relies only on composer for autloading
- PSR4 autoloading
- Zip is now called ZipArchive (BC)
- Caching is now implemented
- compatible with Flysystem 1.0.0 (BC)
- Renamed config keys (BC);
  - bsb_flysystem['adapter_manager']['services'] > bsb_flysystem['adapter_manager']['config']
  - bsb_flysystem['filesystem_manager']['services'] > bsb_flysystem['filesystem_manager']['config']

v0.1.0 initial pre-release
