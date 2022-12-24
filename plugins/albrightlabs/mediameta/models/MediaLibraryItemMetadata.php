<?php
namespace Albrightlabs\MediaMeta\Models;

use Model;
use BackendAuth;
use Config;
use Carbon\Carbon;
use Backend\Models\User as BackendUser;


class MediaLibraryItemMetadata extends Model {

	protected $table = 'albrightlabs_mediameta';
	protected $primaryKey = 'id';

	protected $fillable = ['filepath','description','user_id'];

	public $belongsTo = [
		'user' => ['Backend\Models\User']
	];

	public function getImageAttribute($value){
		$mediaFolder = Config::get('cms.storage.media.path');

		return '<img src="' . $mediaFolder . $this->filepath .'" alt="'.$this->title.'" class="w-full img-thumbnail"/>';
	}
}
