<?php

class imap_driver{

		private $fp;
		public $error;

		private $command_counter = "00000001";
		public $last_response = array();
		public $last_endline = "";

		public function init($host, $port)
		{
			
			if (!($this->fp = fsockopen($host, $port, $errno, $errstr, 15))) {
				$this->error = "Could not connect to host ($errno) $errstr";
				return false;
			}
			if (!stream_set_timeout($this->fp, 15)) {
				$this->error = "Could not set timeout";
				return false;
			}
			$line = fgets($this->fp);
			return true;
		}
		
		private function close()
		{
			fclose($this->fp);
		}

		private function command($command)
		{

			$this->last_response = array();
			$this->last_endline  = "";
			fwrite($this->fp, "$this->command_counter $command\r\n");          // send the command
			while ($line = fgets($this->fp)) {                                   // fetch the response one line at a time
				$line = trim($line);                                             // trim the response
				$line_arr = preg_split('/\s+/', $line, 0, PREG_SPLIT_NO_EMPTY);  // split the response into non-empty pieces by whitespace
				
				if (count($line_arr) > 0) {
					$code = array_shift($line_arr);                              // take the first segment from the response, which will be the line number
					
					if (strtoupper($code) == $this->command_counter) {
						$this->last_endline = join(' ', $line_arr);              // save the completion response line to parse later
						break;
					} else {
						$this->last_response[] = $line;                          // append the current line to the saved response
					}
					
				} else {
					$this->last_response[] = $line;
				}
			}
			
			$this->increment_counter();
		}

		private function increment_counter()
		{
			$this->command_counter = sprintf('%08d', intval($this->command_counter) + 1);
		}


		public function login($login, $pwd){
        $this->command("LOGIN $login $pwd");

		print_r($this);
		die();
        if (preg_match('~^OK~', $this->last_endline)) {
            return true;
        } else {
            $this->error = join(', ', $this->last_response);
            $this->close();
            return false;
        }
    }
}
?>
