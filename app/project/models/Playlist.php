<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 17:02
 */

namespace app\project\models;


use app\core\db\builder\DeleteQuery;
use app\core\db\builder\SelectQuery;
use app\core\exceptions\ApplicationException;
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
     * @throws ApplicationException
     * @throws UnauthorizedException
     */
    public function __construct($obj) {
        if (is_array($obj)) {
            $this->playlist = $obj;
        } else if (is_scalar($obj)) {
            $this->playlist = PlaylistDao::get($obj);
        } else {
            throw new ApplicationException("Wrong type of argument");
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

    public static function removeLinks($link_id) {
        $links = explode(",", $link_id, substr_count($link_id, ","));
        (new SelectQuery(TPlaylistSongLinks::_NAME))
            ->where(TPlaylistSongLinks::LINK_ID, $links)
            ->innerJoin(TPlaylists::_NAME, TPlaylists::ID, TPlaylistSongLinks::PLAYLIST_ID)
            ->select(TPlaylists::USER_ID)
            ->eachRow(function ($row) {
                if ($row[TPlaylists::USER_ID] != self::$me->getId()) {
                    throw new UnauthorizedException("Sorry, but you trying to delete tracks from other user's playlist");
                }
            });
        (new DeleteQuery(TPlaylistSongLinks::_NAME))
            ->where(TPlaylistSongLinks::LINK_ID, $links)
            ->update();
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
        $export = $this->playlist;
        $export["playlist_url"] = "playlist/" . $this->playlist["id"];
        return $export;
    }


} 