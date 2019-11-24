<?php

include('sezerclass.php');

$imap_driver = new imap_driver();

$imap_driver->init('ssl://imap.gmail.com',993);
if ($imap_driver->login('username', 'password') === false) {
    echo "login() failed: " . $imap_driver->error . "\n";
    exit;
}

?>
