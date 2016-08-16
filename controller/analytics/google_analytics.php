<?php

class ControllerAnalyticsGoogleAnalytics extends \Core\Controller {

    public function index() {
        return html_entity_decode($this->config->get('google_analytics_code'), ENT_QUOTES, 'UTF-8');
    }

}
