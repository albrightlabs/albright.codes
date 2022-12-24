<?php
namespace Albrightlabs\MediaMeta\Classes;

use Input;
use ApplicationException;
use Exception;
use Lang;
use Cms\Classes\Theme;
use Cms\Classes\Asset;
use Backend\Classes\WidgetBase;
use Albrightlabs\MediaMeta\Models\MediaLibraryItemMetadata as Metadata;

class MediaMeta {


  protected $theme;

	public function  __construct() {

      $this->theme = Theme::getEditTheme();

  		if (class_exists('System'))  {
    			$manager = \Media\Widgets\MediaManager::class;
    			$libraryItem = \System\Classes\MediaLibraryItem::class;
  		} else {
    			$manager = \Backend\Widgets\MediaManager::class;
    			$libraryItem = \System\Classes\MediaLibraryItem::class;
  		}


  		$manager::extend(function($widget) {

      		$widget->addViewPath(plugins_path().'/albrightlabs/mediameta/partials/editor/');
          $widget->addViewPath(plugins_path().'/albrightlabs/mediameta/partials/');
          $widget->addJs('/plugins/albrightlabs/mediameta/assets/js/metadata.js');
          $widget->addCss('/plugins/albrightlabs/mediameta/assets/css/metadata.css');

          $widget->addDynamicMethod('onLoadMetadataPopup', function() use ($widget) {

              $path = Input::get('path');
              if (!$this->validatePath($path)) {
                  throw new ApplicationException(Lang::get('cms::lang.asset.invalid_path'));
              }

              $metadata = Metadata::where('filepath',$path)->first();
              $widget->vars['description'] = $metadata->description ?? '';

              $widget->vars['originalPath'] = $path;
              $widget->vars['name'] = basename($path);
              $widget->vars['theme'] = $this->theme;
              $widget->vars['exif'] = @exif_read_data(storage_path('app/media' . $path));

              $widget->vars['is_image'] = is_array(getimagesize(storage_path('app/media' . $path))) ? true : false;

      				if (class_exists('System'))  {
      					  return $widget->makePartial(plugins_path().'/albrightlabs/mediameta/partials/editor/update_metadata.htm', ['data' => Input::all(), 'theme' => $this->theme]);
      				} else {
      			      return $widget->makePartial(plugins_path().'/albrightlabs/mediameta/partials/editor/update_metadata', ['data' => Input::all(), 'theme' => $this->theme]);
      				}
          });

          $widget->addDynamicMethod('onApplyMetadataUpdate', function() use ($widget){
            	$path = Input::get('path');
            	$metadata = Metadata::where('filepath','like', '%'.$path)->first();
            	if($metadata) {
        		      $metadata->description = Input::get('description');
            	} else {
              		$metadata = new Metadata;
              		$metadata->filepath = $path;
              		$metadata->description = Input::get('description');
    	        }
      	      $metadata->save();
          });

          $widget->addDynamicMethod('onLoadCropPopup', function() use ($widget){
      				if (class_exists('System'))  {
              		return $widget->makePartial(plugins_path().'/albrightlabs/mediameta/partials/editor/crop_image.htm', ['data' => Input::all()]);
      				} else {
              		return $widget->makePartial(plugins_path().'/albrightlabs/mediameta/partials/editor/crop_image', ['data' => Input::all()]);
      				}
          });


    			$widget->bindEvent('file.rename', function ($originalPath, $newPath) {
    	        // Update custom references to path here
    	        $origFile = Metadata::where('filepath', 'like', '%'.basename($originalPath))->first();
    	        if($origFile) {
    			        $origFile->filepath = '/'.basename($newPath);
    			        $origFile->save();
    			    }
    	    });

  	      $widget->bindEvent('file.move', function ($originalPath, $newPath) {
      				$filename = basename($originalPath);
              $origFile = Metadata::where('filepath', 'like', '%'.basename($originalPath))->first();
              if($origFile) {
      	          $origFile->filepath = $newPath.'/'.$filename;
      	          $origFile->save();
              }
  		    });

    			$widget->bindEvent('file.delete', function ($originalPath) {
    	        // Update custom references to path here
    	        $origFile = Metadata::where('filepath', 'like', '%'.basename($originalPath))->first();
    	        if($origFile) {
      		        $origFile->delete();
      		    }
    	    });

  	      $widget->bindEvent('folder.rename', function ($originalPath, $newPath) {
  		        $items = Metadata::where('filepath','like', '%'.$originalPath.'%')->get();
  		        foreach($items as $item) {
    		        	$item->filepath = str_replace($originalPath, $newPath, $item->full_path);
    		        	$item->save();
  		        }
  		    });

    			$widget->bindEvent('folder.delete', function ($originalPath) {
    	        // Update custom references to path here
    	        $items = Metadata::where('filepath','like', '%'.$originalPath.'%')->delete();
    	    });

		  });
	  }


    protected function validatePath($path)
    {
        if (!preg_match('/^[0-9a-z\.\s_\-\/]+$/i', $path)) {
            return false;
        }

        if (strpos($path, '..') !== false || strpos($path, './') !== false) {
            return false;
        }

        return true;
    }
}
