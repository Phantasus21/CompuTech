<?php
/**
 * Created by PhpStorm.
 * User: sklo
 * Date: 24.05.2018
 * Time: 18:48
 */

class AbstractDAO
{

    private $user = "bpohn2";
    private $pwd = "orcldbpwd";
    private $host = "infdb.technikum-wien.at:1521/o10";
    protected $conn = null;
    private
        $mitarbeiterArray = array();

    protected function doConnect()
    {
        $this->conn = oci_connect($this->user, $this->pwd, $this->host);
    }

    protected function closeConnect()
    {
        $this->conn = close();
    }


}