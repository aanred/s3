<?php

namespace Cilex\Command;

/**
 * S3 Config
 */
class S3Config
{
	public static $REGION = 'us-east-1';

	protected $credentials;

	protected $client;

    /**
     * summary
     */
    public function __construct($region = '')
    {
    	$this->credentials = new \Aws\Credentials\Credentials(ACCESS_KEY_ID, SECRET_ACCESS_KEY);
    	$this->client = new \Aws\S3\S3Client([
		    'version'     => 'latest',
		    'region'      => $region ?: self::$REGION,
		    'credentials' => $this->credentials
		]);
    }

    public function credentials() 
    {
    	return $this->credentials;
    }

    public function client()
    {
    	return $this->client;
    }
}
