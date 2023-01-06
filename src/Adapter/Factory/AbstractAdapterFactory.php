<?php

/**
 * BsbFlystem
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see       https://bushbaby.nl/
 *
 * @copyright Copyright (c) 2014 bushbaby multimedia. (https://bushbaby.nl)
 * @author    Bas Kamer <baskamer@gmail.com>
 * @license   MIT
 *
 * @package   bushbaby/flysystem
 */

declare(strict_types=1);

namespace BsbFlysystem\Adapter\Factory;

use Laminas\Stdlib\ArrayUtils;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;
use League\Flysystem\ReadOnly\ReadOnlyFilesystemAdapter;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use Psr\Container\ContainerInterface;

abstract class AbstractAdapterFactory
{
    /**
     * @var array
     */
    protected $options;

    public function __construct(array $options = [])
    {
        $this->setCreationOptions($options);
    }

    /**
     * Set creation options.
     *
     * @codeCoverageIgnore
     */
    public function setCreationOptions(array $options): void
    {
        $this->options = $options;

        if (\array_key_exists('prefix', $this->options)) {
            \assert(
                \is_string($this->options['prefix']) && ! empty(trim($this->options['prefix'], '/')),
                "Option 'prefix' must be a non empty string and may not be enclosed with a '/'"
            );

            $this->options['prefix'] = trim($this->options['prefix'], '/');
        }

        if (\array_key_exists('readonly', $this->options)) {
            \assert(
                \is_bool($this->options['readonly']),
                "Option 'readonly' must be a boolean"
            );
        }
    }

    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): FilesystemAdapter
    {
        if (null !== $options) {
            $this->setCreationOptions($options);
        }

        $this->mergeMvcConfig($container, $requestedName);

        $this->validateConfig();

        // support path prefixing
        $prefix = class_exists(PathPrefixedAdapter::class) && ($this->options['prefix'] ?? false);
        if ($prefix) {
            $prefix = $this->options['prefix'];

            // prefix may still be an adapter option even without PathPrefixedAdapter class for some adapters
            unset($this->options['prefix']);
        }

        // support read only
        $readonly = class_exists(ReadOnlyFilesystemAdapter::class) && ($this->options['readonly'] ?? false);

        // never pass readonly as adapter option
        unset($this->options['readonly']);

        $adapter = $this->doCreateService($container);

        if ($prefix) {
            $adapter = new PathPrefixedAdapter($adapter, $prefix);
        }

        if ($readonly) {
            $adapter = new ReadOnlyFilesystemAdapter($adapter);
        }

        return $adapter;
    }

    /**
     * Merges the options given from the ServiceLocator Config object with the create options of the class.
     */
    protected function mergeMvcConfig(ContainerInterface $container, string $requestedName = null): void
    {
        $config = $container->has('config') ? $container->get('config') : [];

        if (! isset($config['bsb_flysystem']['adapters'][$requestedName]['options']) ||
            ! \is_array(($config['bsb_flysystem']['adapters'][$requestedName]['options']))
        ) {
            return;
        }

        $this->options = ArrayUtils::merge(
            $config['bsb_flysystem']['adapters'][$requestedName]['options'],
            $this->options
        );
    }

    /**
     * @throws \InvalidArgumentException
     *
     * @codeCoverageIgnore
     */
    public function getLazyFactory(ContainerInterface $container): LazyLoadingValueHolderFactory
    {
        $config = $container->has('config') ? $container->get('config') : [];

        $config['lazy_services'] = ArrayUtils::merge(
            $config['lazy_services'] ?? [],
            $config['bsb_flysystem']['adapter_manager']['lazy_services']
        );

        if (! isset($config['lazy_services'])) {
            throw new \InvalidArgumentException('Missing "lazy_services" config key');
        }

        $lazyServices = $config['lazy_services'];

        $factoryConfig = new Configuration();

        if (isset($lazyServices['proxies_namespace'])) {
            $factoryConfig->setProxiesNamespace($lazyServices['proxies_namespace']);
        }

        if (isset($lazyServices['proxies_target_dir'])) {
            $factoryConfig->setProxiesTargetDir($lazyServices['proxies_target_dir']);
        }

        if (! isset($lazyServices['write_proxy_files']) || ! $lazyServices['write_proxy_files']) {
            $factoryConfig->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        }

        spl_autoload_register($factoryConfig->getProxyAutoloader());

        return new LazyLoadingValueHolderFactory($factoryConfig);
    }

    /**
     * Create service.
     */
    abstract protected function doCreateService(ContainerInterface $container): FilesystemAdapter;

    /**
     * Implement in adapter.
     *
     * @throw UnexpectedValueException
     */
    abstract protected function validateConfig(): void;
}
