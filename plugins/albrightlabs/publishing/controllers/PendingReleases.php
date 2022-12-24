<?php namespace Albrightlabs\Publishing\Controllers;

use Flash;
use Backend;
use Redirect;
use BackendMenu;
use System\Classes\SettingsManager;
use Backend\Classes\Controller;

/**
 * PendingReleases Backend Controller
 */
class PendingReleases extends Controller
{

    public $implement = [
        \Backend\Behaviors\FormController::class,
        \Backend\Behaviors\ListController::class
    ];

    /**
     * @var string formConfig file
     */
    public $formConfig = 'config_form.yaml';

    /**
     * @var string listConfig file
     */
    public $listConfig = 'config_list.yaml';

    /**
     * @var array required permissions
     */
    public $requiredPermissions = ['albrightlabs.publishing.manage_pending_releases',];

    /**
     * __construct the controller
     */
    public function __construct()
    {
        parent::__construct();

        BackendMenu::setContext('October.System', 'system', 'settings');
        SettingsManager::setContext('Albrightlabs.Publishing', 'pendingreleases');
    }

    /**
     * Preview context delete
     */
    public function preview_onDelete($context = null)
    {
        parent::update_onDelete($context);

        return Redirect::to(Backend::url('albrightlabs/publishing/pendingreleases'));
    }
}
