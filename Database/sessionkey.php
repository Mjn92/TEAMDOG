#!/usr/bin/php
<?php

function generateSessionKey(){
	return bin2hex(random_bytes(32));
}
?>
