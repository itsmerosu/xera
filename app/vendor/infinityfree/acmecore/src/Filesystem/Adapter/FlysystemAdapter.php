<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Filesystem\Adapter;

use InfinityFree\AcmeCore\Filesystem\FilesystemInterface;
use League\Flysystem\FilesystemInterface as FlysystemFilesystemInterface;

class FlysystemAdapter implements FilesystemInterface
{
    /**
     * @var FlysystemFilesystemInterface
     */
    private $filesystem;

    public function __construct(FlysystemFilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function write(string $path, string $content)
    {
        $isOnRemote = $this->filesystem->has($path);
        if ($isOnRemote && !$this->filesystem->update($path, $content)) {
            throw $this->createRuntimeException($path, 'updated');
        }
        if (!$isOnRemote && !$this->filesystem->write($path, $content)) {
            throw $this->createRuntimeException($path, 'created');
        }
    }

    public function delete(string $path)
    {
        $isOnRemote = $this->filesystem->has($path);
        if ($isOnRemote && !$this->filesystem->delete($path)) {
            throw $this->createRuntimeException($path, 'delete');
        }
    }

    public function createDir(string $path)
    {
        $isOnRemote = $this->filesystem->has($path);
        if (!$isOnRemote && !$this->filesystem->createDir($path)) {
            throw $this->createRuntimeException($path, 'created');
        }
    }

    private function createRuntimeException(string $path, string $action): \RuntimeException
    {
        return new \RuntimeException(
            sprintf(
                'File %s could not be %s because: %s',
                $path,
                $action,
                error_get_last()
            )
        );
    }
}
