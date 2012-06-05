<?php

/**
 * @file
 * Defines RedmineRecord.
 */

abstract class RedmineRecord {
  static $element_name;
  static $element_plural_name;

  protected $data = array();
  protected $connection;

  public function __construct(RedmineConnection $connection, array $data = array()) {
    $this->connection = $connection;
    $this->data = $data;
  }

  public function __get($name) {
    if (array_key_exists($name, $this->data)) {
      return $this->data[$name];
    }
  }

  public function __set($name, $value) {
    $this->data[$name] = $value;
  }

  public function __isset($name) {
    return isset($this->data[$name]);
  }

  public function __unset($name) {
    unset($this->data[$name]);
  }

  protected function setConnection(RedmineConnection $connection) {
    $this->connection = $connection;
  }

  public function getConnection() {
    return $this->connection;
  }

  protected function setData(array $data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  public static function load(RedmineConnection $connection, $id, array $options = array()) {
    if (!is_numeric($id)) {
      throw new RedmineException('Invalid load ID ' . $id);
    }

    $class = get_called_class();
    $resource = $class::$element_plural_name . '/' . $id;
    $response = $connection->request($resource);
    if (!empty($response->data[$class::$element_name])) {
      return new $class($connection, $response->data[$class::$element_name]);
    }
    return FALSE;
  }

  public static function loadMultiple(RedmineConnection $connection, array $options = array()) {
    $class = get_called_class();
    $response = $connection->request($class::$element_plural_name, $options);
    $count = 0;
    foreach ($response->data[$class::$element_plural_name] as $key => $record) {
      $response->data[$class::$element_plural_name][$key] = new $class($connection, $record);
      $count++;
    }
    //$response->data['count'] = $count;
    return $response->data;
  }

  public function save(array $options = array()) {
    $options['method'] = !empty($this->id) ? 'PUT' : 'POST';
    $options['data'][$this::$element_name] = $this->data;
    $resource = $this::element_plural_name . (!empty($this->id) ? '/' . $this->id : '');
    $response = $this->connection->request($resource, $options);
    $this->data = $response->data['data'];
    return TRUE;
  }

  public function delete(array $options = array()) {
    if (!empty($this->id)) {
      $options['method'] = 'DELETE';
      $resource = $this::$element_plural_name . '/' . $this->id;
      $response = $this->connection->request($resource, $options);
    }
    $this->data = array();
    return TRUE;
  }
}
