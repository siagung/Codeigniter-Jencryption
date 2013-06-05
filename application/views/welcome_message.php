<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Welcome to CodeIgniter</title>

    <style type="text/css">

        ::selection {
            background-color: #E13300;
            color: white;
        }

        ::moz-selection {
            background-color: #E13300;
            color: white;
        }

        ::webkit-selection {
            background-color: #E13300;
            color: white;
        }

        body {
            background-color: #fff;
            margin: 40px;
            font: 13px/20px normal Helvetica, Arial, sans-serif;
            color: #4F5155;
        }

        a {
            color: #003399;
            background-color: transparent;
            font-weight: normal;
        }

        h1 {
            color: #444;
            background-color: transparent;
            border-bottom: 1px solid #D0D0D0;
            font-size: 19px;
            font-weight: normal;
            margin: 0 0 14px 0;
            padding: 14px 15px 10px 15px;
        }

        code {
            font-family: Consolas, Monaco, Courier New, Courier, monospace;
            font-size: 12px;
            background-color: #f9f9f9;
            border: 1px solid #D0D0D0;
            color: #002166;
            display: block;
            margin: 14px 0 14px 0;
            padding: 12px 10px 12px 10px;
        }

        #body {
            margin: 0 15px 0 15px;
        }

        p.footer {
            text-align: right;
            font-size: 11px;
            border-top: 1px solid #D0D0D0;
            line-height: 32px;
            padding: 0 10px 0 10px;
            margin: 20px 0 0 0;
        }

        #container {
            margin: 10px;
            border: 1px solid #D0D0D0;
            -webkit-box-shadow: 0 0 8px #D0D0D0;
        }
    </style>
</head>
<body>

<div id="container">
    <h1>String Encryption Using jCryption JavaScript data encryption. <code><a href="http://www.jcryption.org/">http://www.jcryption.org/</a></code>
    </h1>

    <div id="body">
        <?php echo form_open(current_url()); echo form_close();?>
        <code><p id="status">
                <span style="font-size: 16px;">Encrypting channel ...</span>
                <img src="<?php echo base_url(); ?>assets/images/loading.gif" alt="Loading..." title="Loading..."
                     style="margin-right:15px;"/>
            </p>
            String:
            <input type="text" id="text" style="width: 433px" disabled="disabled"/>
            <button id="encrypt" disabled="disabled" style="padding-right: 5px">encrypt</button>
            <button id="decrypt" disabled="disabled" style="padding-right: 5px">decrypt</button>
            <button id="serverChallenge" disabled="disabled" style="padding-right: 5px">get encrypted time from server
            </button>
            <br/>
        </code>

        <p>Log:<br/>
            <textarea cols="300" rows="10" id="log" style="width: 500px"></textarea>
        </p>
    </div>

    <p class="footer">Page rendered in <strong>{elapsed_time}</strong> seconds</p>
</div>
<script src="<?php echo base_url(); ?>assets/js/jquery-1.9.1.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery.jcryption.js"></script>
<script type="text/javascript">
    !function ($) {
        var $loader = $('<img src="<?php echo base_url();?>assets/images/loading.gif" alt="Loading..." title="Loading..." style="margin-right:15px;" />');
        $(function () {
            var token = $('input[name=csrf_jencrypt_name]').val();
            var hashObj = new jsSHA('<?php  echo $sesstoken;?>', "ASCII");
            var password = hashObj.getHash("SHA-512", "HEX");


            $.jCryption.authenticate(password, "welcome/generateKeypair", "welcome/handshake/", token, function (AESKey) {
                $("#text,#encrypt,#decrypt,#serverChallenge").attr("disabled", false);
                //alert(token);
                $("#status").html('<span style="font-size: 16px;">Let\'s Rock!</span>');
            }, function () {
                // Authentication failed

            });

            $("#encrypt").click(function () {
                var encryptedString = $.jCryption.encrypt($("#text").val(), password);
                $("#log").prepend("\n").prepend("----------");
                $("#log").prepend("\n").prepend("String: " + $("#text").val());
                $("#log").prepend("\n").prepend("Encrypted: " + encryptedString);
                $.ajax({
                    url: "welcome/encryptest",
                    dataType: "json",
                    type: "POST",
                    data: {
                        jCryption: encryptedString, csrf_jencrypt_name: token
                    },
                    success: function (response) {
                        $("#log").prepend("\n").prepend("Server decrypted: " + response.data);
                    }
                });
            });

            $("#serverChallenge").click(function () {
                $.ajax({
                    url: "welcome/decrypttest",
                    dataType: "json",
                    type: "POST",
                    data: {
                        csrf_jencrypt_name: token
                    },
                    success: function (response) {
                        $("#log").prepend("\n").prepend("----------");
                        $("#log").prepend("\n").prepend("Server original: " + response.unencrypted);
                        $("#log").prepend("\n").prepend("Server sent: " + response.encrypted);
                        var decryptedString = $.jCryption.decrypt(response.encrypted, password);
                        $("#log").prepend("\n").prepend("Decrypted: " + decryptedString);
                    }
                });
            });

            $("#decrypt").click(function () {
                var decryptedString = $.jCryption.decrypt($("#text").val(), password);
                $("#log").prepend("\n").prepend("----------");
                $("#log").prepend("\n").prepend("Decrypted: " + decryptedString);
            });

        });
    }(window.jQuery);

</script>
</body>
</html>