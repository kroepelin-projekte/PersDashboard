<?php

include_once "./Services/UIComponent/classes/class.ilUIHookPluginGUI.php";
include_once "class.ilPersDashboardPlugin.php";
include_once "class.ilPersDashboardContent.php";
include_once "./include/inc.ilias_version.php";
include_once "./Services/Language/classes/class.ilLanguage.php";

/**
 * Class ilUsrWelcUIHookGUI
 */
class ilPersDashboardUIHookGUI extends ilUIHookPluginGUI
{
    const COMPONENT_DASHBOARD = "Services/Dashboard"; // ILIAS 6, ILIAS 7
    const COMPONENT_PERSONAL_DESKTOP = "Services/PersonalDesktop"; //ILIAS 5.4
    const COMPONENT_CONTAINER = "Services/Container";
    const PART_RIGHT_COLUMN = "right_column";
    const PART_CENTER_COLUMN = "center_column";

    private function getActiveLang(): string
    {
        $langObj = new ilLanguage("en");

        return $langObj->getUserLanguage();
    }

    /**
     * @param string $a_comp
     * @param string $a_part
     * @param array $a_par
     * @return array
     */
    public function getHTML($a_comp, $a_part, $a_par = array())
    {
        global $DIC;

        // Get active languages
        $langKey = $this->getActiveLang();

        $ilUser = $DIC->user();

        if($ilUser->getID() != ANONYMOUS_USER_ID) {   // Check if not anonymous user

            $persDashboardCont = new ilPersDashboardContent();

            $contentConfig = $this->plugin_object->getConfig()->getValueTextarea("content_".$langKey);
            $cssConfig = $this->plugin_object->getConfig()->getValueTextarea("css");

            if (ILIAS_VERSION_NUMERIC >= 6) {

                // Component Dashboard in ILIAS 6 and ILIAS 7
				if ($a_comp === self::COMPONENT_DASHBOARD && $a_part === self::PART_CENTER_COLUMN) {

					return array(
						"mode" => self::APPEND,
						"html" => $persDashboardCont->getContent($contentConfig, $cssConfig)
					);

				}
                

            } elseif (ILIAS_VERSION_NUMERIC >= 5.4) {

                // Component PersonalDesktop in ILIAS54
				if ($a_comp === self::COMPONENT_PERSONAL_DESKTOP && $a_part === self::PART_CENTER_COLUMN) {

					return array(
						"mode" => self::PREPEND,
						"html" => $persDashboardCont->getContent($contentConfig, $cssConfig)
					);

				}
            }
        }
    }
}
