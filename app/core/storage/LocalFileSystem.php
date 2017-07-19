<?php
/**
 * Copyright (c) 2017 Roman Lakhtadyr
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace app\core\storage;

class LocalFileSystem implements FileSystem
{
    /**
     * @var string
     */
    private $rootDir;

    /**
     * @param string $rootDir
     */
    public function __construct(string $rootDir)
    {
        $this->rootDir = $rootDir;
    }

    /**
     * @inheritDoc
     */
    public function save(string $path, $content, array $metadata): void
    {
        $this->createDirsFor($path);
        $this->withFiles($path, function (string $contentFile, string $metadataFile) use ($content, $metadata) {
            file_put_contents($contentFile, $content);
            file_put_contents($metadataFile, json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        });
    }

    /**
     * @inheritDoc
     */
    public function delete(string $path): void
    {
        $this->withFiles($path, function (string $contentFile, string $metadataFile) {
            unlink($contentFile);
            unlink($metadataFile);
        });
    }

    /**
     * @inheritDoc
     */
    public function get(string $path): File
    {
        if (!$this->exists($path)) {
            throw new FileSystemException("File not found: $path");
        }

        return $this->withFiles($path, function (string $contentFile, string $metadataFile) {
            $metadata = json_decode(file_get_contents($metadataFile), true);
            return new File(function () use ($contentFile) {
                return fopen($contentFile, 'r');
            }, $metadata);
        });
    }

    /**
     * @inheritDoc
     */
    public function exists(string $path): bool
    {
        return $this->withFiles($path, function (string $contentFile) {
            return file_exists($contentFile);
        });
    }

    /**
     * Create directory for uploaded file
     * @param string $path
     */
    private function createDirsFor(string $path): void
    {
        $this->withFiles($path, function ($contentFile) {
            $parentDir = pathinfo($contentFile, PATHINFO_DIRNAME);
            if (file_exists($parentDir)) {
                return;
            }
            if (!mkdir($parentDir, 0777, true)) {
                throw new FileSystemException(
                    "Could not create directory for content file: ${contentFile}"
                );
            }
        });
    }

    /**
     * @param string $path
     * @return string
     */
    private function normalize(string $path): string
    {
        return rtrim($this->rootDir, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . ltrim($path, DIRECTORY_SEPARATOR);
    }

    private function withFiles(string $path, callable $callable)
    {
        $normalizedPath = $this->normalize($path);
        $contentFile = $normalizedPath . '.blob';
        $metadataFile = $normalizedPath . '.metadata';
        return $callable($contentFile, $metadataFile);
    }
}
