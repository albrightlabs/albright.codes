<?php namespace Albrightlabs\Publishing\Widgets;

use Flash;
use Config;
use Request;
use Redirect;
use CmsCompoundObject;
use ApplicationException;
use ValidationException;
use Albrightlabs\Publishing\Models\ScheduledRelease;
use Albrightlabs\Publishing\Classes\TimeZoneManager;
use Backend\Models\User;
use Backend\Classes\WidgetBase;

class ScheduleRelease extends WidgetBase
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'schedulerelease';

    /**
     * Opens popup to schedule a release
     */
    public function onOpenSchedulePopup()
    {
        $data = post();
        $this->vars['plugincode'] = $data['plugincode'];
        $this->vars['modelname'] = $data['modelname'];
        $this->vars['modelid'] = $data['modelid'];
        $this->vars['adminid'] = $data['adminid'];
        return $this->makePartial('popup_schedule_release');
    }

    /**
     * Schedules something for later publishing/release
     */
    public function onScheduleRelease()
    {
        $data = post();

        // if(!isset($data['date']) || $data['date'] == ''){
        //     throw new ValidationException(['date' => 'The date field is required.']);
        // }
        // if(!isset($data['time']) || $data['time'] == ''){
        //     throw new ValidationException(['time' => 'The time field is required.']);
        // }
        //
        // $release = new ScheduledRelease;
        // $release->plugincode = $data['plugincode'];
        // $release->modelname = $data['modelname'];
        // $release->modelid = $data['modelid'];
        // $release->adminid = $data['adminid'];
        // $release->admin = User::find($data['adminid']);
        // $type = \Request::input('objectType');
        // trace_log($type);
        // trace_log(trim(\Request::input('objectPath')));
        // return;
        // $controller = \RainLab\Pages\Controllers\Index::class;
        // $object = $controller->fillObjectFromPost($type);

        $type = \Request::input('objectType');
        $object = $this->fillObjectFromPost($type);

        trace_log($object);
        // $release->publish_at = date('Y-m-d H:i:s', strtotime(explode(' ', $data['date'])[0].' '.explode(' ', $data['time'])[1]));
        // $release->save();

        // Flash::success('Release scheduled!');
        // return Redirect::refresh();
    }

    /**
     * Gets a release for loading into popup
     */
    public function onGetReleaseDetails()
    {
        $data = post();
        if($release = ScheduledRelease::find($data['release_id'])){

            return $this->makePartial('release_details', ['release' => $release,]);

        }

        Flash::success('Couldn\'t find release!');
    }

    /**
     * Deletes a scheduled release
     */
    public function onDelete()
    {
        $data = post();
        if($release = ScheduledRelease::find($data['release_id'])){

            $release->delete();
            Flash::success('Release deleted!');
            return Redirect::refresh();

        }

        Flash::success('Couldn\'t find release!');
    }






































    protected function fillObjectFromPost($type)
    {
        $objectPath = trim(Request::input('objectPath'));
        $object = $objectPath ? $this->controller->loadObject($type, $objectPath) : $this->controller->createObject($type);

        // Set page layout super early because it cascades to other elements
        if ($type === 'page' && ($layout = post('viewBag[layout]'))) {
            $object->getViewBag()->setProperty('layout', $layout);
        }

        $formWidget = $this->makeObjectFormWidget($type, $object, Request::input('formWidgetAlias'));

        $saveData = $formWidget->getSaveData();
        $postData = post();
        $objectData = [];

        if ($viewBag = array_get($saveData, 'viewBag')) {
            $objectData['settings'] = ['viewBag' => $viewBag];
        }

        $fields = ['markup', 'code', 'fileName', 'content', 'itemData', 'name'];

        if ($type != 'menu' && $type != 'content') {
            $object->parentFileName = Request::input('parentFileName');
        }

        foreach ($fields as $field) {
            if (array_key_exists($field, $saveData)) {
                $objectData[$field] = $saveData[$field];
            }
            elseif (array_key_exists($field, $postData)) {
                $objectData[$field] = $postData[$field];
            }
        }

        if ($type == 'page') {
            $placeholders = array_get($saveData, 'placeholders');

            $comboConfig = Config::get('cms.convertLineEndings', Config::get('system.convert_line_endings', false));
            if (is_array($placeholders) && $comboConfig === true) {
                $placeholders = array_map([$this, 'convertLineEndings'], $placeholders);
            }

            $objectData['placeholders'] = $placeholders;
        }

        if ($type == 'content') {
            $fileName = $objectData['fileName'];

            if (dirname($fileName) == 'static-pages') {
                throw new ApplicationException(trans('rainlab.pages::lang.content.cant_save_to_dir'));
            }

            $extension = pathinfo($fileName, PATHINFO_EXTENSION);

            if ($extension === 'htm' || $extension === 'html' || !strlen($extension)) {
                $objectData['markup'] = array_get($saveData, 'markup_html');
            }
        }

        if ($type == 'menu') {
            // If no item data is sent through POST, this means the menu is empty
            if (!isset($objectData['itemData'])) {
                $objectData['itemData'] = [];
            } else {
                $objectData['itemData'] = json_decode($objectData['itemData'], true);
                if (json_last_error() !== JSON_ERROR_NONE || !is_array($objectData['itemData'])) {
                    $objectData['itemData'] = [];
                }
            }
        }

        $comboConfig = Config::get('cms.convertLineEndings', Config::get('system.convert_line_endings', false));
        if (!empty($objectData['markup']) && $comboConfig === true) {
            $objectData['markup'] = $this->convertLineEndings($objectData['markup']);
        }

        if (!Request::input('objectForceSave') && $object->mtime) {
            if (Request::input('objectMtime') != $object->mtime) {
                throw new ApplicationException('mtime-mismatch');
            }
        }

        $object->fill($objectData);

        /*
         * Rehydrate the object viewBag array property where values are sourced.
         */
        if ($object instanceof CmsCompoundObject && is_array($viewBag)) {
            $object->viewBag = $viewBag + $object->viewBag;
        }

        return $object;
    }

    protected function loadObject($type, $path, $ignoreNotFound = false)
    {
        $class = $this->resolveTypeClassName($type);

        if (!($object = call_user_func(array($class, 'load'), $this->theme, $path))) {
            if (!$ignoreNotFound) {
                throw new ApplicationException(trans('rainlab.pages::lang.object.not_found'));
            }

            return null;
        }

        return $object;
    }

    protected function createObject($type)
    {
        $class = $this->resolveTypeClassName($type);

        if (!($object = $class::inTheme($this->getController()->theme))) {
            throw new ApplicationException(trans('rainlab.pages::lang.object.not_found'));
        }

        return $object;
    }

    protected function resolveTypeClassName($type)
    {
        $types = [
            'page'    => 'RainLab\Pages\Classes\Page',
            'menu'    => 'RainLab\Pages\Classes\Menu',
            'content' => 'RainLab\Pages\Classes\Content'
        ];

        if (!array_key_exists($type, $types)) {
            throw new ApplicationException(Lang::get('rainlab.pages::lang.object.invalid_type') . ' - type - ' . $type);
        }

        $allowed = false;
        if ($type === 'content') {
            $allowed = $this->controller->user->hasAccess('rainlab.pages.manage_content');
        } else {
            $allowed = $this->controller->user->hasAccess("rainlab.pages.manage_{$type}s");
        }

        if (!$allowed) {
            throw new ApplicationException(Lang::get('rainlab.pages::lang.object.unauthorized_type', ['type' => $type]));
        }

        return $types[$type];
    }











}
