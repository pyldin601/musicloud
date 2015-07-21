<?php
/**
 * Created by PhpStorm
 * User: Roman
 * Date: 02.07.2015
 * Time: 16:59
 */


use app\project\handlers\dynamic\content\DoReadTrack;


when("content/track/&id", DoReadTrack::class);