<?php

/**
 * @package Paymorrow Gateway
 * @copyright (C) 2015 - Paymorrow GmbH
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

class RequestController
{
    private $gateway;
    private $resourceProxy;

    public function pmVerify($data)
    {
        $_SESSION["pm_verify"] = $data;

        return $this->gateway->prepareOrder($data);
    }

     /**
    * $paymentMethod - possible values: 'INVOICE', 'SDD'
	* $order_id - if final order_id is know in this step of confirmation process place it here
    */	
	public function pmConfirm($paymentMethod, $order_id = null)
	{
		return $this->gateway->confirmOrder($paymentMethod, $order_id);
	}
	
    public function getResource($path, $session_id = null)
    {
        return $this->resourceProxy->getResource($path, $session_id);
    }

    /**
     * @param mixed $gateway
     */
    public function setGateway($gateway)
    {
        $this->gateway = $gateway;
    }

    /**
     * @return mixed
     */
    public function getGateway()
    {
        return $this->gateway;
    }

    /**
     * @param mixed $resourceProxy
     */
    public function setResourceProxy($resourceProxy)
    {
        $this->resourceProxy = $resourceProxy;
    }

}

