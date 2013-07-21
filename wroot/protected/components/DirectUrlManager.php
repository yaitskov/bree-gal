<?php

class DirectUrlManager extends CUrlManager {
    public $showScriptName = false;

    /**
     *  @param string $url  somethin like thi http://vk.com/go/there
     *  @param array  $params assoc array of http params
     *  @return string final url http://vk.com/there?a=b+b&c=g+f
     */
    public function urlOfAnotherDomain($url, array $params) {
        return $url . $this->genGetQuery($params);
    }

    public function genGetQuery(array $params) {
        $query = Yii::app()->urlManager->createPathInfo($params, '=', '&');
        if ($query !== '') {
            return '?' . $query;
        }
        return $query;
    }
}

?>