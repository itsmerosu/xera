<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Challenge\Http;

use InfinityFree\AcmeCore\Challenge\ConfigurableServiceInterface;
use InfinityFree\AcmeCore\Challenge\SolverInterface;
use InfinityFree\AcmeCore\Filesystem\Adapter\NullAdapter;
use InfinityFree\AcmeCore\Filesystem\FilesystemFactoryInterface;
use InfinityFree\AcmeCore\Filesystem\FilesystemInterface;
use InfinityFree\AcmeCore\Protocol\AuthorizationChallenge;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\ServiceLocator;
use Webmozart\Assert\Assert;

/**
 * ACME HTTP solver through ftp upload.
 *
 * @author Jérémy Derussé <jeremy@derusse.com>
 */
class FilesystemSolver implements SolverInterface, ConfigurableServiceInterface
{
    /**
     * @var ContainerInterface
     */
    private $filesystemFactoryLocator;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @var HttpDataExtractor
     */
    private $extractor;

    public function __construct(ContainerInterface $filesystemFactoryLocator = null, HttpDataExtractor $extractor = null)
    {
        $this->filesystemFactoryLocator = $filesystemFactoryLocator ?: new ServiceLocator([]);
        $this->extractor = $extractor ?: new HttpDataExtractor();
        $this->filesystem = new NullAdapter();
    }

    public function configure(array $config)
    {
        Assert::keyExists($config, 'adapter', 'configure::$config expected an array with the key %s.');

        /** @var FilesystemFactoryInterface $factory */
        $factory = $this->filesystemFactoryLocator->get($config['adapter']);
        $this->filesystem = $factory->create($config);
    }

    /**
     * {@inheritdoc}
     */
    public function supports(AuthorizationChallenge $authorizationChallenge): bool
    {
        return 'http-01' === $authorizationChallenge->getType();
    }

    /**
     * {@inheritdoc}
     */
    public function solve(AuthorizationChallenge $authorizationChallenge)
    {
        $checkPath = $this->extractor->getCheckPath($authorizationChallenge);
        $checkContent = $this->extractor->getCheckContent($authorizationChallenge);

        $this->filesystem->write($checkPath, $checkContent);
    }

    /**
     * {@inheritdoc}
     */
    public function cleanup(AuthorizationChallenge $authorizationChallenge)
    {
        $checkPath = $this->extractor->getCheckPath($authorizationChallenge);

        $this->filesystem->delete($checkPath);
    }
}
