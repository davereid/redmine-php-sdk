<?php

/**
 * @file
 * Defines RedmineConnection.
 */

class RedmineConnection {

  private $userAgent = 'Redmine PHP SDK';

  private $server;

  private $key;

  private $options = array();

  /**
   * Construct the API object.
   */
  public function __construct($server, $key) {
    $this->setServer($server);
    $this->setKey($key);
  }

  public function setServer($server) {
    if (!filter_var($server, FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED)) {
      throw new RedmineException("Invalid Redmine server $server");
    }

    $this->server = rtrim($server, '/');
  }

  public function getServer() {
    return $this->server;
  }

  public function setKey($key) {
    $this->key = $key;
  }

  public function getKey() {
    return $this->key;
  }

  public function getOptions() {
    return $this->options;
  }

  public function setOptions(array $options) {
    $this->options = $options;
  }

  /**
   * Construct the request URI.
   *
   * @param string $resource
   *   The resource name/path.
   * @param array $query
   *   An array of query string parameters to add to the URL.
   *
   * @return
   *   A fully-quantified Toggl API URL.
   */
  public function getURL($resource, array $query = array()) {
    $url = $this->getServer() . '/' . $resource . '.json';
    if (!empty($query)) {
      $url .= '?' . http_build_query($query, NULL, '&');
    }
    return $url;
  }

  /**
   * Build the request headers.
   *
   * @return array
   */
  protected function getHeaders() {
    return array(
      'X-Redmine-API-Key' => $this->getKey(),
      //'User-Agent' => 'Redmine PHP SDK (+https://github.com/davereid/redmine-php-sdk)',
    );
  }

  public function request($url, array $options = array()) {
    $options += $this->getOptions() + array(
      'headers' => array(),
      'method' => 'GET',
      'data' => NULL,
    );

    // Set the CURL variables.
    $ch = curl_init();

    // Include post data.
    if (isset($options['data'])) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($options['data']));
      $options['headers']['Content-Type'] = 'application/json';
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $options['method']);
    //curl_setopt($ch, CURLOPT_USERPWD, $this->getKey() . ':api_token');

    // Build and format the headers.
    foreach (array_merge($this->getHeaders(), $options['headers']) as $header => $value) {
      $options['headers'][$header] = $header . ': ' . $value;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);

    // Perform the API request.
    $result = curl_exec($ch);
    if ($result == FALSE) {
      throw new RedmineException(curl_error($ch));
    }

    // Build the response.
    $response = new stdClass();
    $response->data = json_decode($result, TRUE);
    $response->code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $response->success = $response->code == 200;

    curl_close($ch);
    return $response;
  }

  public function getCurrentUser(array $options = array()) {
    $response = $this->request($this->getURL('users/current'), $options);
    if (!empty($response->data['user'])) {
      return new RedmineUser($this, $response->data['user']);
    }
    return FALSE;
  }
}
