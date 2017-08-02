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

namespace app\project\handlers\fixed\api\track;

use app\core\db\builder\InsertQuery;
use app\core\view\JsonResponse;
use app\project\models\single\LoggedIn;
use app\project\models\tracklist\Songs;
use app\project\persistence\db\tables\TVideoDlQueue;

class DoCreateFromVideo
{
    public function doPost($video_url, JsonResponse $response, LoggedIn $user)
    {
        $song_id = Songs::create();
        try {
            $query = new InsertQuery(TVideoDlQueue::_NAME);
            $query->values([
                TVideoDlQueue::STATUS => 1,
                TVideoDlQueue::URL => $video_url,
                TVideoDlQueue::USER_ID => $user->getId(),
                TVideoDlQueue::TRACK_ID => $song_id
            ]);
            $query->returning(TVideoDlQueue::ID);
            $id = $query->fetchColumn()->get();
            $response->write(["id" => $id], 200);
        } catch (\Exception $exception) {
            Songs::delete($song_id);
            $response->write(["error" => $exception->getMessage()], 400);
        }
    }
}
