<?php

require_once __DIR__ . "./../init.php";

class Node {
	public $char;
	public $frequency;
	public $left;
	public $right;
	public $encoding;
}

class Encoding 
{
	
	public $input;
	public $output;
	public $success;
	public $errorMsg;
	public $frequencies;
	public $inputLength;
	public $tree;
	public $leaves;
	public $bit;

	/**
	 *
	 * Class constructor
	 *
	**/ 
	public function __construct() {

		$this->frequencies = array();
		$this->tree = array();
		$this->input = "";
		$this->inputLength = 0;
		$this->leaves = array();
		$this->bit = array();
		$this->bit[0] = "0";
		$this->bit[1] = "1";

	}

	/**
	 *
	 * Set the input
	 * 
	 * @param string $input
	 * @return bool
	 *
	**/ 
	public function SetInput($input) {

		if(!$this->ValidateInput($input)) {
			return false;
		} else {
			$this->input = $input;
		}
		$this->ComputeFrequencies();
		return true;

	}

	/**
	 *
	 * Get the output
	 *
	 * @return string 
	 *
	**/
	public function GetOutput() {

		return $this->output;

	}

	/**
	 *
	 * Get the value of $this->errorMsg
	 *
	 * @return string (can be empty)
	 *
	**/
	public function GerError() {

		return $this->errorMsg;

	}

	/**
	 *
	 * Encode the input
	 *
	**/
	public function Encode() {

		$this->SortFrequencies(); // sort the characters by frequency in the input
		$this->tree = array(); // initialize the tree

		foreach($this->frequencies as $char=>$frequency) { // for each distrinct character, create a node

			$tmpNode = new Node();
			$tmpNode->char = $char;
			$tmpNode->frequency = $frequency;
			array_push($this->tree, $tmpNode);

		}

		while(sizeof($this->tree) > 2) {

			$tmpNode = new Node();
			$tmpNode->left = $this->tree[sizeof($this->tree)-1];
			$tmpNode->right = $this->tree[sizeof($this->tree)-2];
			$tmpNode->frequency = $tmpNode->left->frequency + $tmpNode->right->frequency;

			unset($this->tree[sizeof($this->tree)-1]);
			unset($this->tree[sizeof($this->tree)-1]);

			if($tmpNode->frequency <= $this->tree[sizeof($this->tree)-1]->frequency) {

				$rightIndex = sizeof($this->tree);

			} else {

				$rightIndex = sizeof($this->tree)-1;

				for($i = $rightIndex; $i >= 0; $i--) {

					if($this->tree[$i]->frequency < $tmpNode->frequency) {

						$rightIndex = $i;

					} else {

						break;

					}

				}

			}

			for($i = sizeof($this->tree); $i > $rightIndex; $i--) {

				$this->tree[$i] = $this->tree[$i-1];

			}

			$this->tree[$rightIndex] = $tmpNode;

		}

		$tmpNode = new Node();
		$tmpNode->left = $this->tree[1];
		$tmpNode->right = $this->tree[0];
		$this->tree = $tmpNode;

		$this->SetCharEncoding($this->bit[0], $this->tree->left); // get the encoding of each character recursively
		$this->SetCharEncoding($this->bit[1], $this->tree->right);
		
		$this->output = ""; // (re-)initialize the output member

		for($i = 0; $i < $this->inputLength; $i++) { // generate the output

			$this->output .= $this->leaves[$this->input[$i]];

		}

	}

	/**
	 *
	 * Recursive function that sets the encoding for each distinct character
	 * by concatenating 1 or 0 to the end of the encoding of the parent node
	 *
	 * @param string $encoding : the encoding of the parent node
	 * @param node $node : the node of the character generated by the Encode method
	 *
	**/
	private function SetCharEncoding($encoding, &$node) {

		if($node == null) {

			return;

		} else if($node->char == null) {

			$this->SetCharEncoding($encoding . $this->bit[0], $node->left);
			$this->SetCharEncoding($encoding . $this->bit[1], $node->right);

		} else {

			$node->encoding = $encoding;
			$this->leaves[$node->char] = $encoding;

		}

	}

	/**
	 *
	 * Compute the frequencies of each distinct character in $this->input
	 *
	**/
	private function ComputeFrequencies() {

		for($i = 0; $i < $this->inputLength; $i++) {

			if(!isset($this->frequencies[$this->input[$i]])) {

				$this->frequencies[$this->input[$i]] = 1;

			} else {

				$this->frequencies[$this->input[$i]] += 1;

			}

		}

	}

	/**
	 *
	 * Sort the frequencies of the characters
	 *
	**/
	private function SortFrequencies() {

		uasort($this->frequencies, function($a, $b) {
		    return $b - $a;
		});

	}

	/**
	 *
	 * Validate the input, then set $this->success and $this->errorMsg accordingly
	 *
	 * @param string $inputToValidate
	 * @return bool 
	 *
	**/
	private function ValidateInput($inputToValidate) {

		$tmpLength = strlen($inputToValidate);
		
		if($tmpLength < MIN_LEN) {

			$this->success = false;
			$this->errorMsg = "The input must have at least " . MIN_LEN . " characters.";
			return false;

		}
		if($tmpLength > MAX_LEN) {

			$this->success = false;
			$this->errorMsg = "The input can have up to " . MAX_LEN . " characters.";
			return false;

		}

		$this->inputLength = $tmpLength;
		return true;

	}

}
