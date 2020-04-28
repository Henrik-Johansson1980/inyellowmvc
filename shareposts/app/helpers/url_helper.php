<?php 
// Simple redirection to page
function redirect($page){
  header('location: '. URL_ROOT . '/'. $page);
}