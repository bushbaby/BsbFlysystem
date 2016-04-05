# CHANGELOG

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
