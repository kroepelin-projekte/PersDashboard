<?php

include_once "class.ilPersDashboardPlugin.php";

/**
 * Class ilUsrWelcContent
 */
class ilPersDashboardContent
{
    private const TPL_FILE = "tpl.welcome.html";
   
    /**
     * @param string $welcomeText
     * @param string $tplFile
     * @param string $color
     * @return string
     * @throws ilTemplateException
     */
    public function getContent(string $content, string $css)
    {
        global $DIC;

        $ilUser = $DIC->user();
        $picProfile = substr($ilUser->getPersonalPicturePath(), 0, strpos($ilUser->getPersonalPicturePath(), "?"));
        $objIdsCrs = $this->getObjIdCrsUser($ilUser->getId());

        $startCrsToday = 0;
        foreach($objIdsCrs as $objId){

            if($this->getStartDateCrsUser($objId) != NULL){

                if(date("d.m.Y", $this->getStartDateCrsUser($objId)) === date("d.m.Y")){

                    $startCrsToday = $startCrsToday + 1;
                }
            }
        }

        $lastVisitedObject = $this->getLastVisited($ilUser->getId());
        $lastVisitedLink = explode('"', $lastVisitedObject)[13];
        $lastVisitedTitle = explode('"', $lastVisitedObject)[17];

        $userData = [
            "LOGIN_NAME" => $ilUser->getLogin(),
            "FIRST_NAME" => $ilUser->getFirstname(),
            "LAST_NAME" => $ilUser->getLastname(),
            "LAST_LOGIN" => $ilUser->getLastLogin(),
            "CREATE_DATE" => $ilUser->getCreateDate(),
            "FIRST_LOGIN" => substr($ilUser->getFirstLogin(), 0, 10),
            "COUNT_COURSES"=> $this->getCountCrsUser($ilUser->getId()),
            "COUNT_START_COURSES_TODAY" => $startCrsToday,
            "PICTURE_PROFILE_URL" => $picProfile,
            "LAST_VISITED_TITLE" => $lastVisitedTitle,
            "LAST_VISITED_LINK" => $lastVisitedLink,
        ];

        $userBirthday = $ilUser->getBirthday();

        $pl = new ilPersDashboardPlugin();

        foreach($userData as $k => $v){

            if(strpos($content, $k) > -1) {

                $content = str_replace("[" . $k . "]", $v, $content);
            }
        }

        if(strpos($content, "[IF_BIRTHDAY]") > -1) {

            $birthdayWish = substr($content, strpos($content, "[IF_BIRTHDAY]") + 13, strpos($content, "[/IF_BIRTHDAY]") - (strpos($content, "[IF_BIRTHDAY]") + 13));

            if(substr($userBirthday, 5, strlen($userBirthday)) === substr(date("Y-m-d"), 5, strlen(date("Y-m-d")))){

                $content = str_replace("[IF_BIRTHDAY]".$birthdayWish."[/IF_BIRTHDAY]", $birthdayWish, $content);

            }else{

                $content = str_replace("[IF_BIRTHDAY]".$birthdayWish."[/IF_BIRTHDAY]", "", $content);
            }
        }

        if(strpos($content, "[IF_FIRSTLOGIN]") > -1) {

            $welcomeText = substr($content, strpos($content, "[IF_FIRSTLOGIN]") + 15, strpos($content, "[/IF_FIRSTLOGIN]") - (strpos($content, "[IF_FIRSTLOGIN]") + 15));

            if(substr($userData["FIRST_LOGIN"], 0, 10) === date("Y-m-d")){

                $content = str_replace("[IF_FIRSTLOGIN]".$welcomeText."[/IF_FIRSTLOGIN]", $welcomeText, $content);

            }else{

                $content = str_replace("[IF_FIRSTLOGIN]".$welcomeText."[/IF_FIRSTLOGIN]", "", $content);
            }
        }

        if(strpos($content, "[IF_START_COURSES_TODAY]") > -1) {

            $startCoursesToday = substr($content, strpos($content, "[IF_START_COURSES_TODAY]") + 24, strpos($content, "[/IF_START_COURSES_TODAY]") - (strpos($content, "[IF_START_COURSES_TODAY]") + 24));

            if($userData["COUNT_START_COURSES_TODAY"] > 0){

                $content = str_replace("[IF_START_COURSES_TODAY]".$startCoursesToday."[/IF_START_COURSES_TODAY]", $startCoursesToday, $content);

            }else{

                $content = str_replace("[IF_START_COURSES_TODAY]".$startCoursesToday."[/IF_START_COURSES_TODAY]", "", $content);
            }
        }

        if(strpos($content, "[IF_HASPICTUREPROFILE]") > -1) {

            $imgPicProfile = substr($content, strpos($content, "[IF_HASPICTUREPROFILE]") + 22, strpos($content, "[/IF_HASPICTUREPROFILE]") - (strpos($content, "[IF_HASPICTUREPROFILE]") + 22));

            if($userData["PICTURE_PROFILE_URL"] === ""){

                $content = str_replace("[IF_HASPICTUREPROFILE]".$imgPicProfile."[/IF_HASPICTUREPROFILE]", "", $content);

            }else{

                $content = str_replace("[IF_HASPICTUREPROFILE]".$imgPicProfile."[/IF_HASPICTUREPROFILE]", $imgPicProfile, $content);

            }
        }


        $html = $content;

        $tpl = $pl->getTemplate(self::TPL_FILE, true, true);
        $tpl->setCurrentBlock("block_content");
        $tpl->setVariable("WELCOME_CONTENT", $html);
        $tpl->setVariable("CSS_STYLE", $css);

        return $tpl->get();
    }

    /**
     * @param int $idUser
     * @return int|mixed
     */
    protected function getCountCrsUser(int $idUser)
    {
        global $DIC;

        $result = $DIC->database()->queryF("SELECT COUNT(obj_id) AS `count_crs` FROM obj_members WHERE usr_id = %s",
            [ilDBConstants::T_INTEGER],
            [$idUser]);

        $countCrs = 0;
        while ($rec = $result->fetchAssoc())
        {
            $countCrs = $rec["count_crs"];
        }
        return $countCrs;
    }

    /**
     * @param int $idUser
     * @return array
     */
    protected function getObjIdCrsUser(int $idUser)
    {
        global $DIC;

        $result = $DIC->database()->queryF("SELECT obj_id FROM obj_members WHERE usr_id = %s",
            [ilDBConstants::T_INTEGER],
            [$idUser]);

        $objIds = [];
        while ($rec = $result->fetchAssoc())
        {
            $objIds[] = $rec["obj_id"];
        }
        return $objIds;
    }

    /**
     * @param int $idObj
     * @return mixed
     */
    protected function getStartDateCrsUser(int $idObj)
    {
        global $DIC;

        $result = $DIC->database()->queryF("SELECT crs_start FROM crs_settings WHERE obj_id = %s",
            [ilDBConstants::T_INTEGER],
            [$idObj]);

        while ($rec = $result->fetchAssoc())
        {
            $date = $rec["crs_start"];
        }
        return $date;
    }

    /**
     * @param int $idUser
     * @return mixed
     */
    private function getLastVisited(int $idUser)
    {
        global $DIC;

        $res = $DIC->database()->queryF("SELECT last_visited FROM usr_data WHERE usr_id = %s",
            [ilDBConstants::T_INTEGER],
            [$idUser]);

        while ($rec = $res->fetchAssoc())
        {
            return $rec["last_visited"];
        }

    }
}
