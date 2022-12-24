<?php namespace Albrightlabs\Publishing\Widgets;

use Config;
use Backend\Models\User;
use Albrightlabs\Publishing\Models\Preview as PreviewModel;
use Backend\Classes\WidgetBase;

class Preview extends WidgetBase
{
    /**
     * @var string A unique alias to identify this widget.
     */
    protected $defaultAlias = 'preview';

    /**
     * Generates a preview data and gets the user to that page
     */
    public function onGeneratePreview()
    {
        $data = post();

        $preview = new PreviewModel;
        $preview->nonce = $data['adminid'].$data['modelid'].time();
        $preview->plugincode = $data['plugincode'];
        $preview->modelname = $data['modelname'];
        $preview->modelid = $data['modelid'];
        $preview->adminid = $data['adminid'];
        $preview->admin = User::find($data['adminid']);
        if(method_exists($this->controller, 'formGetWidget')){
            $preview->content = $this->controller->formGetWidget()->getSaveData();
        }
        else{
            $preview->content = $this->controller->widget->form->getSaveData();
        }
        $preview->save();

        if($data['modelname'] == 'Services'){ $returnUrl = Config::get('app.frontend_url').'services/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Franchise'){ $returnUrl = Config::get('app.frontend_url').'lawn-care-locations/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Fo'){ $returnUrl = Config::get('app.frontend_url').'franchise-opportunity/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Foblog'){ $returnUrl = Config::get('app.frontend_url').'franchise-opportunity/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'PpcLandingPages'){ $returnUrl = Config::get('app.frontend_url').'ppc-landing-page/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'LawnCareGuides'){ $returnUrl = Config::get('app.frontend_url').'learn/lawn-care-guides/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'TreeCareGuides'){ $returnUrl = Config::get('app.frontend_url').'learn/tree-care-guides/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'PestControlGuides'){ $returnUrl = Config::get('app.frontend_url').'learn/pest-control-guides/'.$preview->content['slug'].'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'AboutUs'){ $returnUrl = Config::get('app.frontend_url').'about-us?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'AboutUsApproach'){ $returnUrl = Config::get('app.frontend_url').'about-us/our-green-approach?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'AboutUsContact'){ $returnUrl = Config::get('app.frontend_url').'about-us/contact-spring-green?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'AntControl'){ $returnUrl = Config::get('app.frontend_url').'services/ant-control?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Career'){ $returnUrl = Config::get('app.frontend_url').'careers?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Homepage'){ $returnUrl = Config::get('app.frontend_url').'?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'LandingPageLearn'){ $returnUrl = Config::get('app.frontend_url').'learn?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'LandingPageLawnCareServices'){ $returnUrl = Config::get('app.frontend_url').'services/lawn-care-services?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'LandingPagePestControlServices'){ $returnUrl = Config::get('app.frontend_url').'services/pest-services?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'LandingPageServices'){ $returnUrl = Config::get('app.frontend_url').'services?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'LandingPageTreeShrubServices'){ $returnUrl = Config::get('app.frontend_url').'services/tree-shrub-services?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'MosquitoControl'){ $returnUrl = Config::get('app.frontend_url').'services/mosquito?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Privacy'){ $returnUrl = Config::get('app.frontend_url').'privacy?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'OutOfArea'){ $returnUrl = Config::get('app.frontend_url').'out-of-area?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'CommercialServices'){ $returnUrl = Config::get('app.frontend_url').'commercial-services?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Survey'){ $returnUrl = Config::get('app.frontend_url').'survey?st=D1096CAC-A3E2-4E15-92AC-8F0589893653&franchise_id=SLI016&c=1548751&preview='.$preview->nonce; }
        elseif($data['modelname'] == 'CheckoutThankYou'){ $returnUrl = Config::get('app.frontend_url').'checkout/thank-you?preview='.$preview->nonce; }
        elseif($data['modelname'] == 'Term'){ $returnUrl = Config::get('app.frontend_url').'learn/glossary/'.$preview->content['slug'].'?preview='.$preview->nonce; }

        return [
            'open_preview_url' => $returnUrl,
        ];
    }
}
