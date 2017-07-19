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

namespace MusicLoud\Tests\Storage;

use app\core\storage\FileSystemException;
use app\core\storage\LocalFileSystem;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class LocalFileSystemTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var LocalFileSystem
     */
    private $fs;

    public function setUp()
    {
        $this->root = vfsStream::setup();
        $this->fs = new LocalFileSystem($this->root->url());
    }

    public function testSaveReadAndFileExists()
    {
        $this->fs->save('foo/bar/baz.txt', 'baz content', ['meta' => 'data']);

        $this->assertTrue($this->fs->exists('foo/bar/baz.txt'));
        $this->assertFalse($this->fs->exists('foo/bar/bass.txt'));

        $file = $this->fs->get('foo/bar/baz.txt');

        $this->assertEquals('data', $file->getMetadata()['meta']);

        $file->withStream(function ($stream) {
            $this->assertEquals('baz content', stream_get_contents($stream));
        });
    }

    public function testGetThatDoesNotExist()
    {
        $this->expectException(FileSystemException::class);
        $this->fs->get('foo');
    }

    public function testBoundaryAndDelete()
    {
        $this->fs->save('../../foo/bar/.././file.txt', '', []);
        $this->assertTrue($this->fs->exists('foo/file.txt'));
        $this->fs->delete('foo/file.txt');
        $this->assertFalse($this->fs->exists('foo/file.txt'));
    }

    public function testUrl()
    {
        $this->assertEquals(
            'vfs://root/foo/bar/baz.txt.blob',
            $this->fs->url('foo/bar/baz.txt')
        );
    }
}
