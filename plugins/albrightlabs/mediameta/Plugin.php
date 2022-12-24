<?php
namespace Albrightlabs\MediaMeta;

use Config;
use Illuminate\Filesystem\Filesystem;
use Backend\Widgets\MediaManager;
use System\Classes\PluginBase;
use Albrightlabs\MediaMeta\Classes\MediaMeta as MetadataController;
use Albrightlabs\MediaMeta\Models\MediaLibraryItemMetadata;

class Plugin extends PluginBase
{

    public function pluginDetails()
    {
        return [
            'name'        => 'Media Metadata',
            'description' => 'A plugin that adds support for media library metadata.',
            'author'      => 'Albright Labs LLC',
            'icon'        => 'icon-leaf',
            'homepage'    => 'https://albrightlabs.com'
        ];
    }

    public function registerMarkupTags()
    {
        return [
            'filters' => [
                'metadata' => [$this, 'getMediaInfo'],
            ]
        ];
    }

    public function getMediaInfo($value) {

        $mediaFolder = Config::get('cms.storage.media.folder', 'media');
        $data = MediaLibraryItemMetadata::where('filepath', 'like','%' . $value)->first();
        if($data) {
            return $data;
        }
    }

    public function boot() {

    	$manager = new MetadataController;
	}

    public function registerPageSnippets() {
        return [
            'Albrightlabs\MediaMeta\Components\Metadata' => 'metaInfo'
        ];
    }

    public function registerComponents() {
        return [
            'Albrightlabs\MediaMeta\Components\Metadata' => 'metaInfo'
        ];
    }

}
