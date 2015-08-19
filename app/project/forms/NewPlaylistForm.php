<?php
/**
 * Created by PhpStorm.
 * User: Roman
 * Date: 19.08.2015
 * Time: 16:49
 */

namespace app\project\forms;


use app\abstractions\AbstractForm;
use app\core\exceptions\ValidatorException;
use app\lang\option\Filter;
use app\lang\option\Option;

class NewPlaylistForm extends AbstractForm {

    /** @var Option */
    protected $name;

    protected $_name;

    public function __construct() {
        parent::__construct();
    }

    public function validate() {
        $this->_name = $this->name
            ->orThrow(ValidatorException::class, "Enter playlist name")
            ->filter(Filter::lengthInRange(1, VALIDATOR_PLAYLIST_NAME_MAX_LENGTH))
            ->getOrThrow(ValidatorException::class, "Playlist name is too long");
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->_name;
    }


} 