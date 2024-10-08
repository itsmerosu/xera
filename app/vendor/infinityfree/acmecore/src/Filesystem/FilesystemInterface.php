<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Filesystem;

interface FilesystemInterface
{
    /**
     * Write content to a file.
     */
    public function write(string $path, string $content);

    /**
     * Delete a file.
     */
    public function delete(string $path);

    /**
     * Delete a directory.
     */
    public function createDir(string $path);
}
