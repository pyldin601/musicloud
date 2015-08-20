<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 17:02
 */

namespace app\project\models;


use app\core\db\builder\SelectQuery;
use app\project\exceptions\UnauthorizedException;
use app\project\models\single\LoggedIn;
use app\project\persistence\db\dao\PlaylistDao;
use app\project\persistence\db\dao\PlaylistSongDao;
use app\project\persistence\db\dao\SongDao;
use app\project\persistence\db\tables\TPlaylists;
use app\project\persistence\db\tables\TPlaylistSongLinks;
use app\project\persistence\db\tables\TSongs;

class Playlist implements \JsonSerializable {

    /** @var LoggedIn */
    private static $me;

    private $playlist;

    public static function class_init() {
        self::$me = resource(LoggedIn::class);
    }

    /**
     * @param array|string $obj
     * @throws UnauthorizedException
     */
    public function __construct($obj) {
        if (is_array($obj)) {
            $this->playlist = $obj;
        } else {
            $this->playlist = PlaylistDao::get($obj);
        }
        $this->checkPermission();
    }

    private function checkPermission() {
        if ($this->playlist[TPlaylists::USER_ID] != self::$me->getId()) {
            throw new UnauthorizedException("You do not have permission to touch this playlist");
        }
    }

    /**
     * @param $name
     * @return Playlist
     */
    public static function create($name) {
        return new self(PlaylistDao::create([
            "user_id" => self::$me->getId(),
            "name" => $name
        ]));
    }

    public function delete() {
        PlaylistDao::delete($this->playlist[TPlaylists::ID]);
    }

    public function removeTracks(array $link_ids) {
        PlaylistSongDao::delete([
            TPlaylistSongLinks::LINK_ID => $link_ids,
            TPlaylistSongLinks::PLAYLIST_ID => $this->playlist[TPlaylists::ID]
        ]);
    }

    public function addTracks($song_ids) {
        $next_order_id = count(PlaylistSongDao::getList([
            TPlaylistSongLinks::PLAYLIST_ID => $this->playlist[TPlaylists::ID]
        ]));
        foreach (explode(",", $song_ids) as $song_id) {
            (new SelectQuery(TSongs::_NAME))
                ->where(TSongs::ID, $song_id)
                ->where(TSongs::USER_ID, self::$me->getId())
                ->select(TSongs::ID)
                ->fetchColumn()
                ->then(function ($id) use (&$next_order_id) {
                    PlaylistSongDao::create([
                        TPlaylistSongLinks::PLAYLIST_ID => $this->playlist[TPlaylists::ID],
                        TPlaylistSongLinks::SONG_ID => $id,
                        TPlaylistSongLinks::ORDER_ID => $next_order_id ++
                    ]);
                });
        }

    }

    /**
     * @return string
     */
    public function getId() {
        return $this->playlist[TPlaylists::ID];
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->playlist[TPlaylists::NAME];
    }

    /**
     * @return int
     */
    public function getOwnerId() {
        return $this->playlist[TPlaylists::USER_ID];
    }

    /**
     * @return array
     */
    public function jsonSerialize() {
        return $this->playlist;
    }


} 