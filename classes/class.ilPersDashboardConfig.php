<?php

include_once "./include/inc.ilias_version.php";

class ilPersDashboardConfig
{
    protected $settings;

    /**
     * ilPersDashboardConfig constructor.
     * @param $settingsId
     */
    public function __construct($settingsId)
    {
        $this->settings = new ilSetting($settingsId);
    }

    /**
     * @param $textarea
     * @return string
     */
    public function getValueTextarea($textarea)
    {
        return $this->settings->get($textarea, '');
    }

    /**
     * @param $textarea
     * @param $value
     */
    public function setValueTextarea($textarea, $value)
    {
        $this->settings->set($textarea, $value);
    }
}
