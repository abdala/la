<?php
/**
 * Query Auth: Signature generation and validation for REST API query authentication
 *
 * @copyright 2013 Jeremy Kendall
 * @license https://github.com/jeremykendall/query-auth/blob/master/LICENSE MIT
 * @link https://github.com/jeremykendall/query-auth
 */

/**
 * Creates signature
 */
class La_QueryAuth_Signer extends \QueryAuth\Signer
{
    /**
     * @var ParameterCollection Request parameter collection
     */
    private $collection;

    /**
     * Public constructor
     *
     * @param ParameterCollection $collection Parameter collection
     */
    public function __construct(\QueryAuth\ParameterCollection $collection)
    {
        $this->collection = $collection;
    }
    
    /**
     * {@inheritDoc}
     */
    public function createSignature($method, $host, $path, $secret, array $params)
    {
        $data = $this->getStringData($method, $host, $path, $secret, $params);
        return \base64_encode(\hash_hmac('sha256', $data, $secret, true));
    }
    
    public function getStringData($method, $host, $path, $secret, array $params)
    {
        $this->collection->setFromArray($params);
        return rawurlencode($method . "&" . $host . $path . "&" . $this->collection->normalize());
    }
}