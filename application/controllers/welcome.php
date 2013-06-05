<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * User: agung
 * Date: 6/4/13
 * Time: 9:47 PM
 * twitter: @agoeng_es
 */

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

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */