<?php
/**
 * Created by PhpStorm.
 * User: dzhuneyt
 * Date: 09.03.19
 * Time: 11:44
 */

namespace tests\helpers;


use Exception;



class Curl extends \linslin\yii2\curl\Curl
{
    /**
     * @param $url
     * @param bool $raw
     * @return mixed
     * @throws Exception
     */
    public function options($url, $raw = true)
    {
        $this->_baseUrl = $url;
        return $this->_httpRequest('OPTIONS', $raw);
    }
}
