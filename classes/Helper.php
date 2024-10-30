<?php
namespace Ekliptor\Wordpress;

class Helper {
	/**
	 * Updates the $key of the order meta with $value only if it doesn't already have the same value.
	 * This is only applicable to metadata with a unique key, otherwise we just always add the value using add_meta_data().
	 * @param \WC_Order $order
	 * @param string $key
	 * @param mixed $value
	 * @return bool True if the value was updated, false otherwise.
	 */
	public static function updateOrderMeta(\WC_Order $order, string $key, $value): bool {
		$currentValue = $order->get_meta($key);
		if ($currentValue === $value)
			return false;
		$order->add_meta_data($key, $value, true);
    	$order->save_meta_data();
    	return true;
	}
	
	/**
	 * Performs a GET request to a rest API and returns the response as PHP object.
	 * @param string $url The URL to call.
	 * @param array $options
	 * @return boolean|mixed The response as PHP object or false on error
	 */
	public static function restApiGet(string $url, array $options = array()) {
		$response = wp_remote_get($url, static::getHttpOptions($options));
		if ($response instanceof \WP_Error) {
			\Cashtippr::notifyErrorExt("Error on HTTP GET $url", $response->get_error_messages());
			return false;
		}
		$responseCode = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body($response);
		if ($responseCode !== 200) {
			\Cashtippr::notifyErrorExt("Invalid HTTP response code $responseCode on GET: $url", $body);
			return false;
		}
		$json = json_decode($body);
		if ($json === null) {
			\Cashtippr::notifyErrorExt("Error decoding JSON with response code $responseCode on GET: $url", $body);
			return false;
		}
		return $json;
	}
	
	/**
	 * Performs a POST request to a rest API and returns the response as PHP object.
	 * @param string $url The URL to call.
	 * @param array $data
	 * @param array $options
	 * @return boolean|mixed The response as PHP object or false on error
	 */
	public static function restApiPost(string $url, array $data = array(), array $options = array()) {
		$wpOptions = static::getHttpOptions($options);
		$wpOptions['body'] = json_encode($data);
		$response = wp_remote_post($url, $wpOptions);
		if ($response instanceof \WP_Error) {
			\Cashtippr::notifyErrorExt("Error on HTTP POST $url", $response->get_error_messages());
			return false;
		}
		$responseCode = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body($response);
		if ($responseCode !== 200) {
			\Cashtippr::notifyErrorExt("Invalid HTTP response code $responseCode on POST: $url", $body);
			return false;
		}
		$json = json_decode($body);
		if ($json === null) {
			\Cashtippr::notifyErrorExt("Error decoding JSON with response code $responseCode on POST: $url", $body);
			return false;
		}
		return $json;
	}
	
	protected static function getHttpOptions(array $options = array()) {
		return array(
				'timeout' => isset($options['timeout']) ? $options['timeout'] : 10, //seconds
				//'user-agent' => isset($options['userAgent']) ? $options['userAgent'] : $this->userAgent,
				//'redirection' => isset($options['maxRedirects']) ? $options['maxRedirects'] : $this->maxRedirects,
				'headers' => array(
					'Content-Type' => 'application/json',
					'Accept' => 'application/json',
					'Cache-Control' => 'no-cache,max-age=0',
				),
			);
	}
}
?>