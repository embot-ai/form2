<?php
/*
 * Copyright 2019 Serge Cornelissen
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

function parse_string(string $str) : array {
	$blocks = block_parser($str);
	//print_r($blocks);

	$script = [];
	$group = "random";
	foreach ($blocks as $n => $row) {
		$obj = [];

		if (count($row) == 1 && strpos($row[0], '//') === 0) {
			$group = "random";
			continue;
		}

		if (count($row) == 1 && strpos($row[0], '/') === 0) {
			$group = $row[0];
			continue;
		}

		$obj["group"] = $group;
		$obj["label"] = $row[0];

		for ($o=1; $o<count($row); $o++) {
			if (strpos($row[$o], '[') === 0) {
				//echo "found at $o\n";
				$verified = verify_parser($row[$o] ?? "");
				//print_r($verified);
				if (isset($verified[0]['segment'])) {
					$obj["answer"] = $verified;
					$obj["fallback"] = $row[$o+1] ?? false;
					break;
				}
			}
			$obj["question"][] = $row[$o];
		}

		$script[] = $obj;
	}
	return $script;
}

function verify_parser(string $str) : array {
	$data = tokenize($str);
	//var_dump($data);

	$actions = [];
	$BLOCK = 0;
	$LEVEL = 0;
	foreach ($data as $token) {
		if ($token->getTokenType() == TokenType::BLOCK_END) {
			$LEVEL = 0;
			$BLOCK++;
		}
		if ($token->getTokenType() == TokenType::PROP) {
			$actions[$BLOCK]["parameter"] = $token->getText();
		}
		if ($token->getTokenType() == TokenType::TAG) {
			$actions[$BLOCK]["store"] = $token->getText();
		}
		if ($token->getTokenType() == TokenType::TEXT) {
			if ($LEVEL == 0) {
				$actions[$BLOCK]["do"] = $token->getText();
			}
			if ($LEVEL == 1) {
				$actions[$BLOCK]["segment"] = $token->getText();
			}
			if ($LEVEL == 2) {
				$actions[$BLOCK]["jump"] = $token->getText();
			}
			$LEVEL++;
		}

	}
	return $actions;
}

function tokenize(string $str) : array {
	$lexer = new TripletParser($str);

	$tokens = [];

	while($lexer->valid()) {
		$triplet = $lexer->current();
		//echo $triplet, "\$\n";

		$arr = preg_split('//u', $triplet, -1, PREG_SPLIT_NO_EMPTY); //multibyte safe split
		//print_r($arr);

		if (count($arr) == 3) {

			if ($arr[0] == '[') {
				//echo "BLOCK_START\n";
				$tokens[] = new Token($arr[0], TokenType::BLOCK_START);
			}


			if (strpos('[{(.', $arr[0]) !== FALSE) {
				//echo "TOKEN_START\n";

				$part = trim($lexer->fetch(0));
				//var_dump($part);
				if (mb_strlen($part) > 0) {
					$tokens[] = new Token($part, TokenType::TEXT);
				}
			}


			if ($arr[0] == '(' || $arr[0] == '{') {
				$lexer->seekToCharList(1, ')}');

				$part = trim($lexer->fetch(0));
				//var_dump($part);
				if (mb_strlen($part) > 0) {
					if ($arr[0] == '(') {
						$tokens[] = new Token($part, TokenType::PROP);
					} else {
						$tokens[] = new Token($part, TokenType::TAG);
					}
				}
			}

			if ($arr[0] == ']') {
				$part = trim($lexer->fetch(0));
				//var_dump($part);
				if (mb_strlen($part) > 0) {
					$tokens[] = new Token($part, TokenType::TEXT);
				}

				//echo "BLOCK_END\n";

				$tokens[] = new Token($arr[0], TokenType::BLOCK_END);
			}

			if ($arr[0] == "\n") {
				break;
			}

		} else {
			break;
		}
		$lexer->next();
	}

	foreach ($tokens as $token) {
		//$token->println();
	}

	return $tokens;
}


/* function block_parser(string $str) : array {
	$lexer = new TripletParser($str);
	$lines = [];
	$block_counter = 0;
	$line_counter = 0;

	while($lexer->valid()) {
		$triplet = $lexer->current();
		//echo $triplet, "\$\n";

		$arr = preg_split('//u', $triplet, -1, PREG_SPLIT_NO_EMPTY); //multibyte safe split
		//print_r($arr);

		if (count($arr) == 3) {

			if ($arr[0] == "\n") {
				$line = trim($lexer->fetch(0));
				//var_dump($line);

				if (mb_strlen($line) > 0) {
					$lines[$block_counter][$line_counter] = $line;
					$line_counter++;
				}

				if ($arr[1] == "\n") {
					$block_counter++;
					$line_counter = 0;
				}
			}

		} else {
			break;
		}
		$lexer->next();
	}
	//var_dump($lines);
	//print_r(array_values($lines));
	return array_values($lines);
}

function trim_trailing_space(string $str) {
	$arr = preg_split('/\n/u', $str);
	$arr = array_map('trim', $arr);
	//print_r($arr);
	//return implode("\n", $arr);
	return $arr;
} */

function block_parser(string $str) : array {
	$arr = preg_split('/\n/u', $str);
	$arr = array_map('trim', $arr);
	$arr = array_values(array_filter($arr, function($val) {
		return !(strpos($val, '#') === 0);
	}));

	$block_counter = 0;
	$line_counter = 0;
	$lines = [];

	$t = count($arr);
	for ($i=0; $i<$t; $i++) {
		$line = $arr[$i];
		//var_dump($line);

		if (mb_strlen($line) > 0) {
			$lines[$block_counter][$line_counter] = $line;
			$line_counter++;
		} else {
			$block_counter++;
			$line_counter = 0;
		}

	}
	//print_r($lines);
	return array_values($lines);
}


