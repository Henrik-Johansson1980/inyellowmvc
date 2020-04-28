<?php

/**
 * App Core Class
 * Creates URL and loads core controller
 * URL FORMAT - /controller/method/parameters
 */

class Core
{
  protected $currentController = 'Pages';
  protected $currentMethod = 'index';
  protected $parameters = [];

  public function __construct()
  {
    $url = $this->getURL();
    // Look in controllers for first value
    if (file_exists('../app/controllers/' . ucwords($url[0]) . '.php')) {
      // If controller exists 
      $this->currentController = ucwords($url[0]);
      // Unset 0 index
      unset($url[0]);
    }

    //Require controller
    require_once '../app/controllers/' . $this->currentController . '.php';
    $this->currentController = new $this->currentController;

    //Check for second part of url
    if (isset($url[1])) {
      //Does method exist in controller?
      if (method_exists($this->currentController, $url[1])) {
        $this->currentMethod = $url[1];
        unset($url[1]);
      }
    }

    // Get parameters
    $this->parameters = $url ? array_values($url) : [];

    // Call a callback with array of parameters
    call_user_func_array([$this->currentController, $this->currentMethod], $this->parameters);
  }

  public function getURL()
  {
    if (isset($_GET['url'])) {
      $url = rtrim($_GET['url'], '/');
      $url = filter_var($url, FILTER_SANITIZE_URL);
      //Break up url into array
      $url = explode('/', $url);
      return $url;
    }
  }
}
