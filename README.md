codeigniter-string-encryption-using-jCryption-JavaScript-data-encryption
========================================================================

codeigniter-string-encryption-using-jCryption-JavaScript-data-encryption is an extended `jCryption` [jcryption.org](http://www.jeasyui.com) library to use in encrytion data on CodeIgniter applications.

created a library Jencryption extends jcryption.php 

## Synopsis

   class Welcome extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url','string','form'));
        $this->load->library('jencryption');
    }

    public function index()
    {
        $this->data['sesstoken'] = random_string('alnum', 16);
        $this->load->view('welcome_message',$this->data);
    }

    public function generateKeypair()
    {
        echo $this->jencryption->get_Keypair();
    }

    public function handshake()
    {
        echo $this->jencryption->get_handshake($this->input->post('key'));
    }

    public function encryptest(){
        echo $this->jencryption->encrypt_key($this->input->post('jCryption'));
    }
    public function decrypttest()
    {
        echo $this->jencryption->decrypt_key($this->input->post('jCryption'));
    }
}

## Usage

1. Drag the **jencryption.php (library), jcryption (folder)** file into CI `application/libraries` folder
2. Drag the **assets (folder)** file into CI root folder
3. Set **$config['encryption_key'] = 'mysuperecryptionkey'** on `application/config/config.php` file
4. Create Multiple session instance using for `jencryption` add this to CI `application/config/config.php` :
```php
  $config['sess_jencrypt'] = array(
   'sess_cookie_name'	=> 'jencryptcicookie',
   'sess_expire_on_close'	=> TRUE,
   'sess_encrypt_cookie'	=> TRUE,
   'sess_expiration' => 900,
   'sess_use_database'	=> FALSE
  );
```
5. I used `csrf_protection` active so add some change to CI `application/config/config.php` file :

    ```php
      $config['csrf_protection'] = TRUE;
      $config['csrf_token_name'] = 'csrf_jencrypt_name';
      $config['csrf_cookie_name'] = 'csrf_jencrypt_cookie_name';
```
     to play with CI csrf_protection on jquery.jcryption.js i add new parameter name "token" :
      ~ base.authenticate = function(token,success, failure) {...}
      ~ $.jCryption.authenticate = function(AESEncryptionKey, publicKeyURL, handshakeURL,token, success, failure){...}
     ```php
      ~ $.jCryption.handshake = function(url, key,token, callback) {
          $.ajax({
              url: url,
              dataType: "json",
              type: "POST",
              data: {
                  key: key, csrf_jencrypt_name:token
              },
              success: function(response) {
                  callback.call(this, response);
              }
          });
      };
     ```

6. add in application crontroller:
    ```php
    $this->load->helper(array('url','string','form'));
    $this->load->library('jencryption');
    ```

7. add `jcryption.js` to views script


## Sample Controller and Views

Controller (welcome.php) and Views (welcome_message.php) modified from string encryption on example folder in jcryption-master download


* Have a nice Day :)