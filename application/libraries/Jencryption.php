<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
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