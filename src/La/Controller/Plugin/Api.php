<?php 
use JSend\JSendResponse;

class La_Controller_Plugin_Api extends \Zend_Controller_Plugin_Abstract
{
    const ApiKey = '98doiOJGF6a1a0252HG';
    const ApiSecret = '63UGHuhu420KJGHa9js16d1df88IHkIBK9';
    
    public function dispatchLoopStartup(\Zend_Controller_Request_Abstract $request)
    {
        Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender();
        
        $module = $request->getModuleName();
        $controller = $request->getControllerName();
        $action = $request->getActionName();

        if ($controller == 'client') {
            return;
        }
        
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/json', true);
        
        $factory    = new \QueryAuth\Factory();
        $signer     = new La_QueryAuth_Signer(new \QueryAuth\ParameterCollection());
        $authServer = new \QueryAuth\Server($signer);
        $key        = self::ApiKey;
        $secret     = self::ApiSecret;
        $params     = $_GET;
        
        if ($request->getMethod() == 'POST') {
            $params = $_POST;
        } 
        
        unset($params['module'], $params['controller'], $params['action']);
        
        try {
            $requestUri = current(explode("?", $request->getRequestUri()));
            
            $authServer->setDrift(200);
            
            $isValid = $authServer->validateSignature(
                $secret,
                $request->getMethod(),
                'http://' . $request->getHttpHost(),
                $requestUri,
                $params
            );
        
            $expires   = date('Y-m-d H:i:s', time() + 3600);
            $signature = new V1_Model_DbTable_Signature();
            
            // $signature->purge();
            // $signature->save($params['key'], $params['signature'], $expires);
            
            if (!$isValid) {
                $response->setHttpResponseCode(200);
                $response->setBody(JSendResponse::fail(array(
                    'message' => 'Assinatura inválida.',
                    'request_signature' => $params['signature']
                )));
                echo $response; exit;
            }
        }  catch (Zend_Db_Exception $e) {
            $this->getResponse()->setHttpResponseCode(200);
            $response->setBody(JSendResponse::fail(array('request' => 'Falha na execução.', 'detail' => $e->getMessage())));
            echo $response; exit;
        } catch (Exception $e) {
            $this->getResponse()->setHttpResponseCode(500);
            $response->setBody(JSendResponse::error($e->getMessage(), $e->getCode()));
            echo $response; exit;
        }
    }
}

