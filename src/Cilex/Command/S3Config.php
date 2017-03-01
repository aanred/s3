<?php

namespace Cilex\Command;

/**
 * S3 Config
 */
class S3Config
{
	public static $ACCESS_KEY_ID = 'AKIAIKB2F3OPOATSTIEA';

	public static $SECRET_ACCESS_KEY = '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';

	public static $REGION = 'us-east-1';

	protected $credentials;

	protected $client;

    /**
     * summary
     */
    public function __construct($region = '')
    {
    	$this->credentials = new \Aws\Credentials\Credentials(self::$ACCESS_KEY_ID, self::$SECRET_ACCESS_KEY);
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
