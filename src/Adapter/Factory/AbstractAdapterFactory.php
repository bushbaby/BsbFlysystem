<?php

namespace BsbFlysystem\Adapter\Factory;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use League\Flysystem\AdapterInterface;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\GeneratorStrategy\EvaluatingGeneratorStrategy;
use ProxyManager\Proxy\VirtualProxyInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\ArrayUtils;

abstract class AbstractAdapterFactory implements FactoryInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * AbstractAdapterFactory constructor.
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setCreationOptions($options);
    }

    /**
     * Set creation options
     *
     * @param  array $options
     * @return void
     */
    public function setCreationOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     * @return AdapterInterface|VirtualProxyInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (null !== $options) {
            $this->setCreationOptions($options);
        }

        $this->mergeMvcConfig($container, $requestedName);

        $this->validateConfig();

        $service = $this->doCreateService($container);

        return $service;
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AdapterInterface|VirtualProxyInterface
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if (method_exists($serviceLocator, 'getServiceLocator')) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        return $this($serviceLocator, func_get_arg(2));
    }

    /**
     * Merges the options given from the ServiceLocator Config object with the create options of the class.
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @param                         string $requestedName
     */
    protected function mergeMvcConfig(ServiceLocatorInterface $serviceLocator, $requestedName)
    {
        $config = $serviceLocator->has('config') ? $serviceLocator->get('config') : [];

        if (!isset($config['bsb_flysystem']['adapters'][$requestedName]['options']) ||
            !is_array(($config['bsb_flysystem']['adapters'][$requestedName]['options']))
        ) {
            return;
        }

        $this->options = ArrayUtils::merge(
            $config['bsb_flysystem']['adapters'][$requestedName]['options'],
            $this->options
        );
    }

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return LazyLoadingValueHolderFactory
     * @throws InvalidArgumentException
     */
    public function getLazyFactory(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->has('config') ? $serviceLocator->get('config') : [];

        $config['lazy_services'] = ArrayUtils::merge(
            isset($config['lazy_services']) ? $config['lazy_services'] : [],
            $config['bsb_flysystem']['adapter_manager']['lazy_services']
        );

        if (!isset($config['lazy_services'])) {
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

        if (!isset($lazyServices['write_proxy_files']) || !$lazyServices['write_proxy_files']) {
            $factoryConfig->setGeneratorStrategy(new EvaluatingGeneratorStrategy());
        }

        spl_autoload_register($factoryConfig->getProxyAutoloader());

        return new LazyLoadingValueHolderFactory($factoryConfig);
    }

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AdapterInterface|VirtualProxyInterface
     */
    abstract protected function doCreateService(ServiceLocatorInterface $serviceLocator);

    /**
     * Implement in adapter
     *
     * @throw UnexpectedValueException
     * @return null
     */
    abstract protected function validateConfig();
}
