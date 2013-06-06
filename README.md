Codeigniter $_POST/$_GET data encryption using jCryption.js
===========================================================

Codeigniter (2.1.3) library for encrypting $_POST/$_GET data using jCryption JavaScript data encryption is an extends of a great jquery plugins `jcryption.php` [jcryption.org](http://www.jcryption.org).

**use multiple session and csrf_protection**

## Synopsis

 ```php
   class Welcome extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->helper(array('url','string','form'));
        $this->load->library('jencryption');
        $this->data = null;
    }

    public function index()
    {
        //generate random key for jcryption hash
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
```

## Usage

1. Drag the **jencryption.php (library),MY_Session.php (library), jcryption (folder)** file into CI `application/libraries` folder
2. Drag the **assets (folder)** file into CI `root` folder
3. Set **$config['encryption_key'] = 'mysuperecryptionkey'** on `application/config/config.php` file
4. Create Multiple session instance using for `jencryption` add this to CI `application/config/config.php` :

    ```php
    $config['sess_jencrypt'] = array(
                'sess_cookie_name'	=> 'jencryptcicookie',
                'sess_expire_on_close'	=> TRUE,
                'sess_encrypt_cookie'	=> TRUE,
                'sess_expiration' => 900,
                'sess_use_database'	=> TRUE, // optional used database to save jcryption session data
                'sess_table_name'	=> 'jencrypt_session' //database table name
    );
    ```

    i got problems with default CI session library when saving jcryption Session Data to a Database, so i add some modification to CI `Session` library
    named it `MY_Session` on `application/libraries`


5. I used `csrf_protection` active so add some change to CI `application/config/config.php` file :

    ```php
      $config['csrf_protection'] = TRUE;
      $config['csrf_token_name'] = 'csrf_jencrypt_name';
      $config['csrf_cookie_name'] = 'csrf_jencrypt_cookie_name';
    ```
     to play with CI csrf_protection on `jquery.jcryption.js` i add new parameter name **token** :

     ```js
      base.authenticate = function(token,success, failure) {...}
      ```
      ```js
      $.jCryption.authenticate = function(AESEncryptionKey, publicKeyURL, handshakeURL,token, success, failure){...}
     ```
     ```js
       $.jCryption.handshake = function(url, key,token, callback) {
          $.ajax({
              url: url,
              dataType: "json",
              type: "POST",
              data: {
                  key: key, csrf_jencrypt_name:token // to play with CI csrf_protection add csrf_jencrypt_name from CI application/config/config.php
              },
              success: function(response) {
                  callback.call(this, response);
              }
          });
      };
     ```

6. Add in application controller:

    ```php
    $this->load->helper(array('url','string','form'));
    $this->load->library('jencryption');
    ```

7. Add `jcryption.js` to views script


## Sample Controller and Views

this repo include sample Controller (welcome.php) and Views (welcome_message.php) modified from string encryption on example folder in jcryption-master download

nb: 
 set `database.php` in `application/config` folder if use database for saving session daata
 to remove index.php from url drag .htaccess to CI `root` folder and edit `application/config/config.php`

```php
$config['base_url'] = 'http://urlhttp.local/jencryption/';
$config['index_page'] = '';
```

* Have a nice Day :)
