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

namespace app\project\libs;

use app\core\cache\TempFileProvider;
use app\project\models\tracklist\Song;

class VideoDownloader
{
    public function getVideoTitle(string $url): string
    {
        $command = sprintf('ytdl %s --info-json', escapeshellarg($url));

        exec($command, $result, $status);

        if ($status !== 0) {
            throw new \RuntimeException("Video info exit status: " . $status);
        }

        $info = json_decode(implode("\n", $result));

        return $info->title;
    }

    public function downloadToSong(string $video_url, string $song_id): void
    {
        $video_title = $this->getVideoTitle($video_url);

        $temp_file = TempFileProvider::generate("ytdl");

        $command = sprintf(
            'ytdl %s | ffmpeg -i - -vn -f mp3 %s',
            escapeshellarg($video_url),
            escapeshellarg($temp_file)
        );

        exec($command, $result, $status);

        if ($status !== 0) {
            throw new \RuntimeException("Video downloader exit status: " . $status);
        }

        (new Song($song_id))->upload($temp_file, "${video_title}.mp3");
    }
}
