<?php
/*
 * Copyright 2019 Serge Cornelissen, Jelmer Idzenga
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace form2;

class TripletParser implements \Iterator {
	private $p = 0; //pointer to char (char number)
	private $str;
	private $LENGTH;

	private $start = 0; //pointer to word

	function __construct($s) {
		//Append 3 extra new-lines to ensure triplet
		$this->str = $s . "\n\n\n";
		$this->LENGTH = mb_strlen($this->str);
	}

	public function valid() {
		return ($this->p < $this->LENGTH);
	}

	public function key() {
		return $this->p;
	}

	public function tail() {
		return mb_substr($this->str, $this->start);
	}

	public function fetch($offset) {
		$word = mb_substr($this->str, $this->start, ($this->p + $offset - $this->start));
		$this->start = $this->p + $offset + 1;
		return $word;
	}

	public function current() {
		return mb_substr($this->str, $this->p, 3);
	}

	public function seekToChar($offset, $end) {
		$this->p += $offset;
		$part = "";
		while ($this->valid()) {
			$c = $this->currentChar();

			if ($c == $end || $c == "\n") {
				break;
			}

			$part .= $c;
			$this->next();
		}
		return $part;
	}

	public function seekToCharList(int $offset, string $mask) : string {
		$this->p += $offset;
		$part = "";
		while ($this->valid()) {
			$c = $this->currentChar();

			if (strpos($mask, $c) !== FALSE || $c == "\n") {
				break;
			}

			$part .= $c;
			$this->next();
		}
		return $part;
	}
	
	public function currentChar() {
		return mb_substr($this->str, $this->p, 1);
	}
	
	//Function to go faster in long words
	//Call after next()
	public function boost() {
		/* $look_ahead = mb_substr($this->str, $this->p, 5);
		if (ctype_alnum($look_ahead) && mb_strlen($look_ahead) == 5) {
			$this->p += 3;
			//echo "JMP({$this->p});\n";
		} */

		$look_ahead = mb_strpos($this->str, ' ', $this->p);
		if ($look_ahead - $this->p >= 5) {
			$piece = mb_substr($this->str, $this->p, $look_ahead - $this->p);
			//var_dump($piece);
			if (ctype_alnum($piece)) {
				$this->p = $look_ahead - 3;
				//echo "JMP({$this->p});\n";
			}
		}
	}

	public function next() {
		$this->p++;
	}

	public function rewind() {
		$this->p = 0;
		$this->start = 0;
	}
}
