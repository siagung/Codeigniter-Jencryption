<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * jCryption
 *
 * PHP version 5.3
 *
 * LICENSE: This source file is subject to version 3.0 of the PHP license
 * that is available through the world-wide-web at the following URI:
 * http://www.php.net/license/3_0.txt.  If you did not receive a copy of
 * the PHP License and are unable to obtain it through the web, please
 * send a note to license@php.net so we can mail you a copy immediately.
 *
 * Many of the functions in this class are from the PEAR Crypt_RSA package ...
 * So most of the credits goes to the original creator of this package Alexander Valyalkin
 * you can get the package under http://pear.php.net/package/Crypt_RSA
 *
 * I just changed, added, removed and improved some functions to fit the needs of jCryption
 *
 * @author     Daniel Griesser <daniel.griesser@jcryption.org>
 * @copyright  2011 Daniel Griesser
 * @license    http://www.php.net/license/3_0.txt PHP License 3.0
 * @version    1.2
 * @link       http://jcryption.org/
 */

/** extends by
 * User: agung
 * Date: 6/4/13
 * Time: 9:47 PM
 * twitter: @agoeng_es
 */

require_once('jcryption/jcryption.php');

class Jencryption extends jcryption
{
    private $keyLength = 1024;
    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->library('MY_Session',$this->CI->config->item('sess_jencrypt'),'jencryptcicookie');
    }

    public function get_Keypair()
    {
        require_once('jcryption/100_1024_keys.inc.php');
        // Pick a random key from the array
        $keys = $arrKeys[mt_rand(0, 100)];

        $this->newdata = array(
            'e'  => array("int" => $keys["e"], "hex" => $this->dec2string($keys["e"], 16)),
            'd'     => array("int" => $keys["d"], "hex" => $this->dec2string($keys["d"], 16)),
            'n' => array("int" => $keys["n"], "hex" => $this->dec2string($keys["n"], 16))
        );

        $this->CI->jencryptcicookie->set_userdata($this->newdata);
        $arrOutput = array(
            "e" => $this->CI->jencryptcicookie->userdata('e')["hex"],
            "n" => $this->CI->jencryptcicookie->userdata('n')["hex"],
            "maxdigits" => intval($this->keyLength*2/16+3)
        );
        // Convert the response to JSON, and send it to the client
        echo json_encode($arrOutput);
    }

    public function get_handshake($key_post)
    {
        // Decrypt the client's request
        $key = $this->decrypt($key_post, $this->CI->jencryptcicookie->userdata('d')["int"], $this->CI->jencryptcicookie->userdata('n')["int"]);
        // Remove the RSA key from the session
        $this->CI->jencryptcicookie->unset_userdata('e');
        $this->CI->jencryptcicookie->unset_userdata('d');
        $this->CI->jencryptcicookie->unset_userdata('n');
        // Save the AES key into the session

        $this->newdata = array(
            'key'  => $key
        );
        $this->CI->jencryptcicookie->set_userdata($this->newdata);
        // JSON encode the challenge
        echo json_encode(array("challenge" => AesCtr::encrypt($key, $key, 256)));
    }

    public function encrypt_key($key_post){
        echo json_encode(array("data" => AesCtr::decrypt($key_post, $this->CI->jencryptcicookie->userdata('key'), 256)));
    }

    public function decrypt_key()
    {
        // Get some test data to encrypt, this is an ISO 8601 timestamp
        $toEncrypt = date("c");
        // JSON encode the timestamp, both encrypted and unencrypted
        echo json_encode(
            array(
                "encrypted" => AesCtr::encrypt($toEncrypt,  $this->CI->jencryptcicookie->userdata('key'), 256),
                "unencrypted" => $toEncrypt
            )
        );
    }
}