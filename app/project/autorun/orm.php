<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 25.08.15
 * Time: 19:54
 */

use app\core\db\LightORM;
use app\project\persistence\orm\Track;

$orm_config = [
    Track::class => [
        '$table' => 'songs',
        '$key' => 'id',
        '$fields' => [
            'user_id'           => null,
            'file_id'           => null,
            'bitrate'           => null,
            'length'            => null,
            'file_name'         => '',
            'format'            => '',
            'track_title'       => '',
            'track_artist'      => '',
            'track_album'       => '',
            'track_genre'       => '',
            'track_number'      => '',
            'track_comment'     => '',
            'track_year'        => '',
            'track_rating'      => null,
            'is_favourite'      => false,
            'is_compilation'    => false,
            'disc_number'       => null,
            'album_artist'      => '',
            'times_played'      => 0,
            'times_skipped'     => 0,
            'created_date'      => null,
            'last_played_date'  => null,
            'small_cover_id'    => null,
            'middle_cover_id'   => null,
            'big_cover_id'      => null,
            'fts_artist'        => '',
            'fts_album'         => '',
            'fts_any'           => '',
            'fts_genre'         => '',
            'preview_id'        => null,
            'peaks_id'          => null
        ],
        '$crud' => [
            '$create'   => 'INSERT INTO {{ table }} ("{{ fields }}") VALUES ({{ values }}) RETURNING {{ key }}',
            '$read'     => 'SELECT "{{ fields }}" FROM {{ table }} WHERE {{ key }} = ?',
            '$update'   => 'UPDATE {{ table }} SET {{ setters }} WHERE {{ key }} = ?',
            '$delete'   => 'DELETE FROM {{ table }} WHERE {{ key }} = ?'
        ]
    ]
];

LightORM::setup($orm_config);