# CHANGELOG

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
