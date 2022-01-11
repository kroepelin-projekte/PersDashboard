<?php

include_once './Services/UIComponent/classes/class.ilUserInterfaceHookPlugin.php';

class ilPersDashboardPlugin extends ilUserInterfaceHookPlugin
{
    const PLUGIN_ID = "pesr_dash";
    const PLUGIN_NAME = "PersDashboard";
    const PLUGIN_CLASS_NAME = self::class;

    /**
     * ilPersDashboardPlugin constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->includePluginClasses();
        $this->config = new ilPersDashboardConfig($this->getSlotId().'_'.$this->getId());
    }

    /**
     * @return string
     */
    public function getPluginName(): string
    {
        return self::PLUGIN_NAME;
    }

    /**
     * Include Plugin' classes
     */
    private function includePluginClasses()
    {
        $this->includeClass("class.ilPersDashboardConfig.php");
    }

    /**
     * @return ilPersDashboardConfig
     */
    public function getConfig()
    {
        return $this->config;
    }
}