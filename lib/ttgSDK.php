<?php

namespace torchlighttechnology;

require 'Error.php';

/**
 * TTG SDK PHP Client
 * @author waffles
 */

class ttgSDK
{
	protected $username;
	protected $password;
	protected $api_host;
	protected $method;

	public function __construct($api_host, $method = 'POST', $username = null, $password = null)
	{
		$this->api_host = $api_host;
		$this->username = $username;
		$this->password = $password;
		$this->method = strtoupper($method);
	}

	public function __call($endpoint, $args)
	{
		// dump the args into payload
		$payload = $args[0];
		// check to see if payload is json
		if (!$this->is_json($payload)) {
			throw new API_Error('Payload must be JSON');
		}
		// make sure the endpoint has a / on the end
		$this->api_host = rtrim($this->api_host, '/') . '/';
		// transform endpoint to match dashed route
		$endpoint = strtolower(preg_replace('%([a-z])([A-Z])%', '\1-\2', $endpoint));

		return $this->api_request($endpoint, $this->method, $payload);
	}

	private function is_json($args)
	{
		json_decode($args);
		if (json_last_error() == JSON_ERROR_NONE) {
			return true;
		}
		return false;
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

		if ($payload) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		}

		if ($payload && ($request == 'POST' || $request == 'PUT')) {
			$http_headers = [
				'Content-Type: application/json',
				'Content-Length: '.strlen($payload),
				'authorization: Basic '. base64_encode($this->username.':'.$this->password)
			];
		} else {
			$http_headers = [
				'authorization: Basic '. base64_encode($this->username.':'.$this->password)
			];
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
