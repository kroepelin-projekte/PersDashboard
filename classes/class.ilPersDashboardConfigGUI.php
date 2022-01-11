<?php

include_once './Services/Component/classes/class.ilPluginConfigGUI.php';
include_once './Services/Language/classes/class.ilLanguage.php';


class ilPersDashboardConfigGUI extends ilPluginConfigGUI
{
    const CONFIG_CONTENT_TAB_ID = "content_tab";
    const CONFIG_STYLE_TAB_ID = "style_tab";
    const CMD_SHOW_CONTENT_CONFIG_FORM = "showContentConfigForm";
    const CMD_SAVE_CONTENT_CONFIG_FORM = "saveContentConfigForm";
    const CMD_SHOW_STYLE_CONFIG_FORM = "showStyleConfigForm";
    const CMD_SAVE_STYLE_CONFIG_FORM = "saveStyleConfigForm";
    const TAB_CONTENT_ID = "content";
    const TAB_SETTINGS = "Settings";
    const TAB_STYLE_ID = "style";


    /**
     * Perform teh Command of Config.
     *
     * @param mixed $cmd
     */
    public function performCommand($cmd)
    {
        $this->initSubTabs();

        switch ($cmd)
        {
            case self::CMD_SHOW_STYLE_CONFIG_FORM:

                $this->showStyleConfigForm();
                break;

            case self::CMD_SAVE_STYLE_CONFIG_FORM:

                $this->saveStyleConfigForm();
                break;

            case self::CMD_SAVE_CONTENT_CONFIG_FORM:

                $this->saveContentConfigForm();
                break;

            case self::CMD_SHOW_CONTENT_CONFIG_FORM:
            default:
                $this->showContentConfigForm();
                break;
        }
    }

    /**
     * Get installed languages in ILIAS Installation
     *
     * @return array
     */
    private function getInstalledLangs(): array
    {
        // A Lang Key should be given
        $langObj = new ilLanguage('en');

        $installedLangs = $langObj->_getInstalledLanguages();  // Get installed languages

        return $installedLangs;
    }

    /**
     * SubTabs init
     *
     * @param string $a_mode
     */
    private function initSubTabs($a_mode = "")
    {
        global $DIC;

        $DIC->tabs()->addSubTab(self::TAB_CONTENT_ID, $this->plugin_object->txt("content_tab"), $DIC->ctrl()->getLinkTargetByClass(self::class, self::CMD_SHOW_CONTENT_CONFIG_FORM ));
        $DIC->tabs()->addSubTab(self::TAB_STYLE_ID, $this->plugin_object->txt("style_tab"), $DIC->ctrl()->getLinkTargetByClass(self::class, self::CMD_SHOW_STYLE_CONFIG_FORM ));
    }

    /**
     * Show TextConfigForm
     *
     * @param ilPropertyFormGUI|null $form
     */
    protected function showContentConfigForm(ilPropertyFormGUI $form = null)
    {
        global $DIC;

        if($form === null)
        {
            $form = $this->buildContentConfigForm();
        }

        $DIC->ui()->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * Save TextConfigForm
     */
    protected function saveContentConfigForm()
    {
        global $DIC;

        $form = $this->buildContentConfigForm();// Build Texts Configuration Form
        $form->setValuesByPost(); // Set values to fields in Texts Configuration Form

        $installedLangs = $this->getInstalledLangs();
        foreach($installedLangs as $langKey) {

            $this->plugin_object->getConfig()->setValueTextarea("content_".$langKey, $form->getItemByPostVar("content_".$langKey)->getValue());

        }

        // Message --> Changes are completed
        ilUtil::sendSuccess($this->plugin_object->txt("config_modified"), true);

        $DIC->ctrl()->redirect($this, self::CMD_SHOW_CONTENT_CONFIG_FORM);

    }

    /**
     * Show StyleConfigForm
     *
     * @param ilPropertyFormGUI|null $form
     */
    protected function showStyleConfigForm(ilPropertyFormGUI $form = null)
    {
        global $DIC;

        if($form === null)
        {
            $form = $this->buildStyleConfigForm();
        }

        $DIC->ui()->mainTemplate()->setContent($form->getHTML());
    }

    protected function saveStyleConfigForm()
    {
        global $DIC;

        $form = $this->buildStyleConfigForm();// Build Style Configuration Form
        $form->setValuesByPost(); // Set values to fields in Style Configuration Form

        if($form === null)
        {
            $form = $this->buildStyleConfigForm();
        }

        $this->plugin_object->getConfig()->setValueTextarea("css", $form->getItemByPostVar("css")->getValue());


        // Message --> Changes are completed
        ilUtil::sendSuccess($this->plugin_object->txt("config_modified"), true);

        $DIC->ctrl()->redirect($this, self::CMD_SHOW_STYLE_CONFIG_FORM);

    }

    protected function buildContentConfigForm()
    {
        global $DIC;

        //Configuration's form
        $form = new ilPropertyFormGUI();
        //The Class, that is used on submitting the configuration's form
        $form->setFormAction($DIC->ctrl()->getFormAction($this, self::CMD_SHOW_CONTENT_CONFIG_FORM));
        //Add Button "Save"
        $form->addCommandButton(self::CMD_SAVE_CONTENT_CONFIG_FORM, $DIC->language()->txt("save"));
        //Add Title
        $form->setTitle($this->plugin_object->txt(self::CONFIG_CONTENT_TAB_ID));
        // Add Description
        $form->setDescription($this->plugin_object->txt("description_text_config"));

        $installedLangs = $this->getInstalledLangs(); // Installed Languages

        // Textareas for activated languages
        foreach($installedLangs as $langKey){

            // Textarea for Welcome Message
            $textareaWelcMsg = new ilTextAreaInputGUI($this->plugin_object->txt("content_".$langKey), "content_".$langKey);
            $textareaWelcMsg->setRows(25);
            $textareaWelcMsg->setValue($this->plugin_object->getConfig()->getValueTextarea("content_".$langKey));
            $textareaWelcMsg->setUseRte(true);
            $textareaWelcMsg->setRteTagSet("full");
            $textareaWelcMsg->addPlugin("latex");
            $textareaWelcMsg->addButton("latex");
            $textareaWelcMsg->addButton("pastelatex");
            $form->addItem($textareaWelcMsg);
        }

        return $form;
    }

    protected function buildStyleConfigForm()
    {
        global $DIC;

        // Configuration's form
        $form = new ilPropertyFormGUI();
        // The Class, that is used on submitting the configuration's form
        $form->setFormAction($DIC->ctrl()->getFormAction($this, self::CMD_SHOW_STYLE_CONFIG_FORM));
        // Add Button "Save"
        $form->addCommandButton(self::CMD_SAVE_STYLE_CONFIG_FORM, $DIC->language()->txt("save"));
        // Add Title
        $form->setTitle($this->plugin_object->txt(self::CONFIG_STYLE_TAB_ID));
        // Add Description
        $form->setDescription($this->plugin_object->txt("description_style_config"));

        $css = new ilTextAreaInputGUI($this->plugin_object->txt("css"), "css");
        $css->setRows(25);
        $css->setValue($this->plugin_object->getConfig()->getValueTextarea("css"));
        $form->addItem($css);

        return $form;
    }
}
