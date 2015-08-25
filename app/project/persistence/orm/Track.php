<?php
/**
 * Created by PhpStorm.
 * User: roman
 * Date: 25.08.15
 * Time: 19:52
 */

namespace app\project\persistence\orm;


use app\core\db\AbstractPersistentObject;

class Track extends AbstractPersistentObject {

    private $id, $user_id, $file_id, $bitrate, $length, $file_name,
        $format, $track_title, $track_artist, $track_album, $track_genre, $track_number, $track_comment,
        $track_year, $track_rating, $is_favourite, $is_compilation, $disc_number, $album_artist, $times_played,
        $times_skipped, $created_date, $last_played_date, $small_cover_id, $middle_cover_id, $big_cover_id,
        $fts_artist, $fts_album, $fts_any, $fts_genre, $preview_id, $peaks_id;

    /**
     * @return mixed
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUserId() {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getFileId() {
        return $this->file_id;
    }

    /**
     * @return mixed
     */
    public function getBitrate() {
        return $this->bitrate;
    }

    /**
     * @return mixed
     */
    public function getLength() {
        return $this->length;
    }

    /**
     * @return mixed
     */
    public function getFileName() {
        return $this->file_name;
    }

    /**
     * @return mixed
     */
    public function getFormat() {
        return $this->format;
    }

    /**
     * @return mixed
     */
    public function getTrackTitle() {
        return $this->track_title;
    }

    /**
     * @return mixed
     */
    public function getTrackArtist() {
        return $this->track_artist;
    }

    /**
     * @return mixed
     */
    public function getTrackAlbum() {
        return $this->track_album;
    }

    /**
     * @return mixed
     */
    public function getTrackGenre() {
        return $this->track_genre;
    }

    /**
     * @return mixed
     */
    public function getTrackNumber() {
        return $this->track_number;
    }

    /**
     * @return mixed
     */
    public function getTrackComment() {
        return $this->track_comment;
    }

    /**
     * @return mixed
     */
    public function getTrackYear() {
        return $this->track_year;
    }

    /**
     * @return mixed
     */
    public function getTrackRating() {
        return $this->track_rating;
    }

    /**
     * @return mixed
     */
    public function getIsFavourite() {
        return $this->is_favourite;
    }

    /**
     * @return mixed
     */
    public function getIsCompilation() {
        return $this->is_compilation;
    }

    /**
     * @return mixed
     */
    public function getDiscNumber() {
        return $this->disc_number;
    }

    /**
     * @return mixed
     */
    public function getAlbumArtist() {
        return $this->album_artist;
    }

    /**
     * @return mixed
     */
    public function getTimesPlayed() {
        return $this->times_played;
    }

    /**
     * @return mixed
     */
    public function getTimesSkipped() {
        return $this->times_skipped;
    }

    /**
     * @return mixed
     */
    public function getCreatedDate() {
        return $this->created_date;
    }

    /**
     * @return mixed
     */
    public function getLastPlayedDate() {
        return $this->last_played_date;
    }

    /**
     * @return mixed
     */
    public function getSmallCoverId() {
        return $this->small_cover_id;
    }

    /**
     * @return mixed
     */
    public function getMiddleCoverId() {
        return $this->middle_cover_id;
    }

    /**
     * @return mixed
     */
    public function getBigCoverId() {
        return $this->big_cover_id;
    }

    /**
     * @return mixed
     * @JsonIgnore
     */
    public function getFtsArtist() {
        return $this->fts_artist;
    }

    /**
     * @return mixed
     * @JsonIgnore
     */
    public function getFtsAlbum() {
        return $this->fts_album;
    }

    /**
     * @return mixed
     * @JsonIgnore
     */
    public function getFtsAny() {
        return $this->fts_any;
    }

    /**
     * @return mixed
     * @JsonIgnore
     */
    public function getFtsGenre() {
        return $this->fts_genre;
    }

    /**
     * @return mixed
     */
    public function getPreviewId() {
        return $this->preview_id;
    }

    /**
     * @return mixed
     */
    public function getPeaksId() {
        return $this->peaks_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id) {
        $this->user_id = $user_id;
    }

    /**
     * @param mixed $file_id
     */
    public function setFileId($file_id) {
        $this->file_id = $file_id;
    }

    /**
     * @param mixed $bitrate
     */
    public function setBitrate($bitrate) {
        $this->bitrate = $bitrate;
    }

    /**
     * @param mixed $length
     */
    public function setLength($length) {
        $this->length = $length;
    }

    /**
     * @param mixed $file_name
     */
    public function setFileName($file_name) {
        $this->file_name = $file_name;
    }

    /**
     * @param mixed $format
     */
    public function setFormat($format) {
        $this->format = $format;
    }

    /**
     * @param mixed $track_title
     */
    public function setTrackTitle($track_title) {
        $this->track_title = $track_title;
    }

    /**
     * @param mixed $track_artist
     */
    public function setTrackArtist($track_artist) {
        $this->track_artist = $track_artist;
    }

    /**
     * @param mixed $track_album
     */
    public function setTrackAlbum($track_album) {
        $this->track_album = $track_album;
    }

    /**
     * @param mixed $track_genre
     */
    public function setTrackGenre($track_genre) {
        $this->track_genre = $track_genre;
    }

    /**
     * @param mixed $track_number
     */
    public function setTrackNumber($track_number) {
        $this->track_number = $track_number;
    }

    /**
     * @param mixed $track_comment
     */
    public function setTrackComment($track_comment) {
        $this->track_comment = $track_comment;
    }

    /**
     * @param mixed $track_year
     */
    public function setTrackYear($track_year) {
        $this->track_year = $track_year;
    }

    /**
     * @param mixed $track_rating
     */
    public function setTrackRating($track_rating) {
        $this->track_rating = $track_rating;
    }

    /**
     * @param mixed $is_favourite
     */
    public function setIsFavourite($is_favourite) {
        $this->is_favourite = $is_favourite;
    }

    /**
     * @param mixed $is_compilation
     */
    public function setIsCompilation($is_compilation) {
        $this->is_compilation = $is_compilation;
    }

    /**
     * @param mixed $disc_number
     */
    public function setDiscNumber($disc_number) {
        $this->disc_number = $disc_number;
    }

    /**
     * @param mixed $album_artist
     */
    public function setAlbumArtist($album_artist) {
        $this->album_artist = $album_artist;
    }

    /**
     * @param mixed $times_played
     */
    public function setTimesPlayed($times_played) {
        $this->times_played = $times_played;
    }

    /**
     * @param mixed $times_skipped
     */
    public function setTimesSkipped($times_skipped) {
        $this->times_skipped = $times_skipped;
    }

    /**
     * @param mixed $created_date
     */
    public function setCreatedDate($created_date) {
        $this->created_date = $created_date;
    }

    /**
     * @param mixed $last_played_date
     */
    public function setLastPlayedDate($last_played_date) {
        $this->last_played_date = $last_played_date;
    }

    /**
     * @param mixed $small_cover_id
     */
    public function setSmallCoverId($small_cover_id) {
        $this->small_cover_id = $small_cover_id;
    }

    /**
     * @param mixed $middle_cover_id
     */
    public function setMiddleCoverId($middle_cover_id) {
        $this->middle_cover_id = $middle_cover_id;
    }

    /**
     * @param mixed $big_cover_id
     */
    public function setBigCoverId($big_cover_id) {
        $this->big_cover_id = $big_cover_id;
    }

    /**
     * @param mixed $preview_id
     */
    public function setPreviewId($preview_id) {
        $this->preview_id = $preview_id;
    }

    /**
     * @param mixed $peaks_id
     */
    public function setPeaksId($peaks_id) {
        $this->peaks_id = $peaks_id;
    }

}