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

/**
 * Created by PhpStorm.
 * User: roman
 * Date: 8/2/17
 * Time: 10:10
 */

namespace app\project\handlers\fixed\cron;

use app\core\db\builder\SelectQuery;
use app\core\db\builder\UpdateQuery;
use app\project\handlers\base\BaseCronRouteHandler;
use app\project\libs\VideoDownloader;
use app\project\persistence\db\tables\TVideoDlQueue;
use malkusch\lock\mutex\FlockMutex;

class DoDownloadVideo extends BaseCronRouteHandler
{
    public function doPost()
    {
        (new FlockMutex(fopen(__FILE__, 'r')))->synchronized(function () {
            $downloader = new VideoDownloader();
            $jobs = (new SelectQuery(TVideoDlQueue::_NAME))
                ->where(TVideoDlQueue::STATUS, 0)
                ->fetchAll();

            foreach ($jobs as $job) {
                try {
                    $downloader->downloadToSong($job['url'], $job['track_id']);
                    $status = 1;
                } catch (\Exception $e) {
                    // todo: add logging (after logger)
                    $status = 2;
                }
                (new UpdateQuery(TVideoDlQueue::_NAME))
                    ->where(TVideoDlQueue::ID, $job[TVideoDlQueue::ID])
                    ->set(TVideoDlQueue::STATUS, $status)
                    ->update();
            }
        });
    }
}
