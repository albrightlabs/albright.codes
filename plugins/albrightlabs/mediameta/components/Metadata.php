<?php
namespace Albrightlabs\MediaMeta\Components;

use Cms\Classes\ComponentBase;
use Albrightlabs\MediaMeta\Models\MediaLIbraryItemMetadata;

class Metadata extends ComponentBase {

	public function componentDetails()
    {
        return [
            'name' => 'albrightlabs.mediameta::lang.component.name',
            'description' => 'albrightlabs.mediameta::lang.component.description'
        ];
    }


    public function defineProperties()
	{
	    return [
	        'file_path' => [
	             'title'             => 'albrightlabs.mediameta::lang.component.file_path',
	             'description'       => 'albrightlabs.mediameta::lang.component.file_path_description',
	             'type'              => 'string',
	             'required'			=> true,
	        ],
	        'tag' => [
	             'title'             => 'albrightlabs.mediameta::lang.component.tag_label',
	             'description'       => 'albrightlabs.mediameta::lang.component.tag_label_description',
	             'default'			=> 'span',
	             'type'              => 'string',
	        ],
	        'css_classes' => [
	             'title'             => 'albrightlabs.mediameta::lang.component.css_classes_label',
	             'description'       => 'albrightlabs.mediameta::lang.component.css_classes_label_description',
	             'type'              => 'string',
	        ],
	        'custom_style' => [
	             'title'             => 'albrightlabs.mediameta::lang.component.custom_style_label',
	             'description'       => 'albrightlabs.mediameta::lang.component.custom_style_label_description',
	             'type'              => 'string',
	        ],
	        'field' => [
	             'title'             => 'albrightlabs.mediameta::lang.component.field',
	             'description'       => 'albrightlabs.mediameta::lang.component.field_description',
	             'default'			=> 'title',
	             'type'              => 'dropdown',
            	'placeholder' => 'Select field',
	             'options' => [
	             	'image' => 'Image tag with src',
	             	'path' => 'Relative media path',
	             	'description' => 'Description metadata',
	             ]
	        ]

	    ];
	}


    public function tag() {
    	return $this->property('tag');
    }

    public function field() {
    	return $this->property('field');
    }

    public function customValue($field) {
    	if($this->property('file_path') != '') {
			$metadata = MediaLIbraryItemMetadata::where('filepath', 'like' ,'%' . $this->property('file_path'))->first();
			if($metadata) {
		        return $metadata->$field;
		    }
		}
    }

    public function path() {
    	return $this->property('file_path');
    }

    public function cssClasses() {
    	return $this->property('css_classes');
    }

    public function styles() {
    	return $this->property('custom_style') ?? false;
    }

    public function value() {
        $field = $this->property('field');
    	if($this->property('file_path') != '') {
			$metadata = MediaLIbraryItemMetadata::where('filepath', 'like' ,'%' . $this->property('file_path'))->first();
			if($metadata) {
		        return $metadata->$field;
		    }
		}
    }
}
