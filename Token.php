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

class Token {
	private $text;
	private $tokenType;

	function __construct(string $text, string $tokenType) {
		$this->text = $text;
		$this->tokenType = $tokenType;
	}

	public function getText() {
		return $this->text;
	}

	public function getTokenType() {
		return $this->tokenType;
	}

	public function println() {
		echo "token", "(\n \"" , $this->text , "\"\n " , $this->tokenType;
		echo "\n)\n";
	}
}
