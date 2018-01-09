<?php

namespace torchlighttechnology;

require 'Error.php';

/**
 * TTG SDK PHP Client
 * @author waffles
 */

class ttgSDK
{
	const HTTP_POST = 'POST';
	const HTTP_GET = 'GET';
	const HTTP_PUT = 'PUT';
	const HTTP_DELETE = 'DELETE';

	protected $username;
	protected $password;
	protected $api_host;

	public function __construct($api_host, $username = null, $password = null)
	{
		$this->api_host = $api_host;
		$this->username = $username;
		$this->password = $password;
	}

	public function __call($name, $args)
	{
        echo "Calling object method '$name' "
             . implode(', ', $arguments). "\n";
	}

	protected function build_path($endpoint)
	{
		$path = sprintf('%s', $endpoint);

		$path = sprintf('%s%s',
			$this->api_host,
			$path
		);

		return $path;
	}

	protected function api_request($endpoint, $request = 'POST', $payload = null)
	{
		$path = $this->build_path($endpoint);

		$ch = curl_init($path);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);

		$payload_string = null;
		if ($payload) {
			$payload_string = json_encode($payload);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload_string);
		}

		if ($payload && ($request == 'POST' || $request == 'PUT')) {
			$http_headers = array(
				'Content-Type: application/json',
				'Content-Length: '.strlen($payload_string),
				'authorization: Basic '. base64_encode($this->username.':'.$this->password)
			);
		} else {
			$http_headers = array(
				'authorization: Basic '. base64_encode($this->username.':'.$this->password)
			);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $http_headers);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		$code = null;
		try {
			$result = curl_exec($ch);
			$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			$response = json_decode($result);

			if ($code != 200) {
				throw new API_Error('Request was not successful', $code, $result, $response);
			}
		} catch (API_Error $e) {
			$response = (object) array(
				'code' => $code,
				'status' => 'error',
				'success' => false,
				'exception' => $e
			);
		}

		curl_close($ch);

		return $response;
	}
}