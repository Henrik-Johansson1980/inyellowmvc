<?php
class Users extends Controller
{
  public function __construct()
  {
    $this->userModel = $this->model('User');
  }

  public function register()
  {
    // Check for post
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Process form
      // Sanitize post data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Init data
      $data = [
        'name' => trim($_POST['name']),
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'confirm_password' => trim($_POST['confirm_password']),
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      // Validate Email
      if (empty($data['email'])) {
        $data['email_err'] = 'Please enter email.';
      } else {
        //Check if email exists in databas
        if ($this->userModel->findUserByEmail($data['email'])) {
          $data['email_err'] = 'Email is already taken';
        }
      }
      // Name Validation
      if (empty($data['name'])) {
        $data['name_err'] = 'Please enter name.';
      }
      // Password Validation
      if (empty($data['password'])) {
        $data['password_err'] = 'Please enter password.';
      } elseif (strlen($data['password']) < 6) {
        $data['password_err'] = 'Password must be at least six characters.';
      }

      // Confirm Password Validation
      if (empty($data['confirm_password'])) {
        $data['confirm_password_err'] = 'Please confirm password.';
      } else {
        if ($data['password'] != $data['confirm_password']) {
          $data['confirm_password_err'] = 'Passwords don\'t match';
        }
      }


      // Make sure errors are empty
      if (empty($data['email_err']) && empty($data['name_err']) && empty($data['password_err']) && empty($data['confirm_password_err'])) {
        // Validated
        // Hash Password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        // Register user 
        if ($this->userModel->register($data)) {
          // Flash message
          flash('register_success', 'You are now registered and can log in');

          //Call to redirect helperfunction and pass in adress to login page
          redirect('users/login');
        } else {
          die('Something went wrong with the registration');
        }
      } else {
        //Load view with errors
        $this->view('users/register', $data);
      }
    } else {
      // Init data
      $data = [
        'name' => '',
        'email' => '',
        'password' => '',
        'confirm_password' => '',
        'name_err' => '',
        'email_err' => '',
        'password_err' => '',
        'confirm_password_err' => ''
      ];

      // Load view
      $this->view('users/register', $data);
    }
  }

  public function login()
  {
    // Check for post
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
      //Process form
      // Sanitize post data
      $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

      // Init data
      $data = [
        'email' => trim($_POST['email']),
        'password' => trim($_POST['password']),
        'email_err' => '',
        'password_err' => '',
      ];

      // Validate Email
      if (empty($data['email'])) {
        $data['email_err'] = 'Please enter email.';
      }
      // Password Validation
      if (empty($data['password'])) {
        $data['password_err'] = 'Please enter password.';
      }

      // Check for user email
      if ($this->userModel->findUserByEmail($data['email'])) {
        //User found
      } else {
        $data['email_err'] = 'No user found';
      }

      //Make sure errors are empty
      if (empty($data['email_err']) and empty($data['password_err'])) {
        // Validated 
        // Check and set logged in user
        $loggedInUser = $this->userModel->login($data['email'], $data['password']);
        if ($loggedInUser) {
          // Create Session variables
          $this->createUserSession($loggedInUser);
        } else {
          $data['password_err'] = 'Password incorrect';
          $this->view('users/login', $data);
        }
      } else {
        //Load view with errors
        $this->view('users/login', $data);
      }
    } else {
      // Init data
      $data = [
        'email' => '',
        'password' => '',
        'email_err' => '',
        'password_err' => '',
      ];

      // Load view
      $this->view('users/login', $data);
    }
  }

  // Create session variable for logged in user
  public function createUserSession($user)
  {
    $_SESSION['user_id'] = $user->id;
    $_SESSION['user_email'] = $user->email;
    $_SESSION['user_name'] = $user->name;
    redirect('posts');
  }

  // Unset _SESSION fields and destroy session
  // and redirect to login page.
  public function logout()
  {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    session_destroy();
    redirect('users/login');
  }
}
