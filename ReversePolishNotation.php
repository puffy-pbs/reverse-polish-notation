<?php

class ReversePolishNotation
{
    /** @var string OPENING_BRACKET */
    private const OPENING_BRACKET= '(';

    /** @var string CLOSING_BRACKET */
    private const CLOSING_BRACKET = ')';

    /** @var string PLUS_SIGN */
    private const PLUS_SIGN = '+';

    /** @var string MINUS_SIGN */
    private const MINUS_SIGN = '-';

    /** @var string MULTIPLY_SIGN */
    private const MULTIPLY_SIGN = '*';

    /** @var string DIVISION_SIGN */
    private const DIVISION_SIGN = '/';

    /** @var string[] CONTROL_CHARACTERS */
    private const CONTROL_CHARACTERS = [
        self::OPENING_BRACKET,
        self::CLOSING_BRACKET,
        self::PLUS_SIGN,
        self::MINUS_SIGN,
        self::MULTIPLY_SIGN,
        self::DIVISION_SIGN,
    ];

    /** @var string[] OPERATORS */
    private const OPERATORS = [
        self::PLUS_SIGN,
        self::MINUS_SIGN,
        self::MULTIPLY_SIGN,
        self::DIVISION_SIGN,
    ];

    /** @var string[] These are the operators which should be executed first (*, /) */
    private const HIGHER_PRIORITY_OPERATORS = [
        self::MULTIPLY_SIGN,
        self::DIVISION_SIGN,
    ];

    /** @var string[] These are the operators which are with lower priority than the ones above (+, -) */
    private const LOWER_PRIORITY_OPERATORS = [
        self::MINUS_SIGN,
        self::PLUS_SIGN,
    ];

    /**
     * The main method of the class
     * @param string $expression
     * @return float
     */
    public function run(string $expression): float
    {
        $preformattedExpression = $this->preformatString($expression);
        if (!$this->isInputValid($preformattedExpression)) {
            throw new InvalidArgumentException('The input string is not correct!');
        }

        // We need to traverse the expression character by character
        $expressionSplit = explode(' ', $preformattedExpression);
        $queue = new SplQueue();
        $stack = new SplStack();

        foreach ($expressionSplit as $character) {
            $character = trim($character);
            if ($this->isOperator($character)) {
                if (!$stack->isEmpty()) {
                    while (!$stack->isEmpty()
                        && $this->isOperator($stack[0])
                        && $this->getPriority($stack[0], $character) > -1) {
                        // Add the operators to the queue if the current character is with higher priority than
                        // the peek of the stack. We need to preserve the correct order
                        $queue->enqueue($stack->pop());
                    }
                }
                // The stack is empty so just push the operator
                $stack->push($character);
            } elseif ($this->isOpeningBracket($character)) { // An opening bracket. Nothing special
                $stack->push($character);
            } elseif ($this->isClosingBracket($character)) {
                while (!$stack->isEmpty() && !$this->isOpeningBracket($stack[0])) {
                    // If we came across closing bracket we should enqueue all items to the queue until we find
                    // the matching opening bracket
                    $queue->enqueue($stack->pop());
                }
                // We pop the matching opening bracket
                $stack->pop();
            } elseif ($this->isOperand($character)) { // Just an operand
                $queue->enqueue($character);
            }
        }

        while (!$stack->isEmpty()) {
            $queue->enqueue($stack->pop());
        }

        return $this->calculateReversePolishNotationExpression($queue);
    }

    /**
     * Check if the input is valid
     * @param $input
     * @return bool
     */
    private function isInputValid($input): bool
    {
        if (!is_string($input)) {
            return false;
        }

        $stack = new SplStack();
        for ($i = 0, $len = strlen($input); $i < $len; $i++) {
            // The opening bracket should be pushed to the stack. No check here
            if ($this->isOpeningBracket($input[$i])) {
                $stack->push($input[$i]);
            } elseif ($this->isClosingBracket($input[$i]) && !$stack->isEmpty()) {
                // If we have closing bracket we should have opening bracket as well in the stack
                $stack->pop();
            } elseif ($this->isControlCharacter($input[$i]) && $this->isControlCharacter($input[$i + 1] ?? ' ')) {
                return false;
            }
        }

        // At this point our stack should be empty (input is valid)
        return $stack->isEmpty();
    }

    /**
     * Is character a control character?
     * @param string $character
     * @return bool
     */
    private function isControlCharacter(string $character): bool {
        return in_array($character, self::CONTROL_CHARACTERS, true);
    }

    /**
     * Is character an opening bracket?
     * @param string $character
     * @return bool
     */
    private function isOpeningBracket(string $character): bool
    {
        return self::OPENING_BRACKET === $character;
    }

    /**
     * Is character a closing bracket?
     * @param string $character
     * @return bool
     */
    private function isClosingBracket(string $character): bool
    {
        return self::CLOSING_BRACKET === $character;
    }

    /**
     * Is character an operator?
     * @param string $character
     * @return bool
     */
    private function isOperator(string $character): bool
    {
        return in_array($character, self::OPERATORS, true);
    }

    /**
     * Get priority based on the function arguments.
     * Multiply and division signs always has precedence than minus and plus
     * @param string $operator
     * @param string $value
     * @return int
     */
    private function getPriority(string $operator, string $value): int
    {
        switch (true) {
            case (in_array($operator, self::HIGHER_PRIORITY_OPERATORS, true)
                    && in_array($value, self::LOWER_PRIORITY_OPERATORS, true)):
                return 1;
            case (in_array($operator, self::LOWER_PRIORITY_OPERATORS, true)
                    && in_array($value, self::HIGHER_PRIORITY_OPERATORS, true)):
                return -1;
            default:
                return 0;
        }
    }

    /**
     * Is the character an operand?
     * @param string $character
     * @return bool
     */
    private function isOperand(string $character): bool
    {
        return !$this->isOperator($character) && is_numeric($character);
    }

    /**
     * A simple formatting to work easily with the values - it just places some spaces
     * @param string $input
     * @return string
     */
    private function preformatString(string $input): string
    {
        $output = [];
        for ($i = 0, $len = mb_strlen($input); $i < $len; $i++) {
            if ($i > 0 &&
                ($this->isOpeningBracket($input[$i - 1]) || $this->isClosingBracket($input[$i]))) {
                $output[] = ' ';
            }
            $output[] = $input[$i];
        }

        return join('', $output);
    }

    /**
     * This function calculates the operation on two operands - "1 / 2", "2 + 1" for an example
     * @param float $firstOperand
     * @param float $secondOperand
     * @param string $operator
     * @return float
     */
    private function calculateIntermediateResult(float $firstOperand, float $secondOperand, string $operator): float
    {
        $result = 0.0;
        switch ($operator) {
            case self::PLUS_SIGN:
                $result = $secondOperand + $firstOperand;
                break;
            case self::MINUS_SIGN:
                $result = $secondOperand - $firstOperand;
                break;
            case self::MULTIPLY_SIGN:
                $result = $secondOperand * $firstOperand;
                break;
            case self::DIVISION_SIGN:
                $result = $secondOperand / $firstOperand;
                break;
        }

        return $result;
    }

    /**
     * This is the mecca of the class. It calculates the RPN
     * @param SplQueue $queue
     * @return float
     */
    private function calculateReversePolishNotationExpression(SplQueue $queue): float
    {
        $stack = new SplStack();
        while (!$queue->isEmpty()) {
            $character = $queue->dequeue();
            if ($this->isOperand($character)) { // Keep on pushing operands for further calculations
                $stack->push($character);
            } elseif ($stack->count() > 1) {
                // If we are at this point we already have two operands and the $character variable represents the operator
                $firstOperand = $stack->pop();
                $secondOperand = $stack->pop();
                $intermediateResult = $this->calculateIntermediateResult($firstOperand, $secondOperand, $character);
                // After the intermediate result is being calculated push it onto the stack
                $stack->push($intermediateResult);
            }
        }

        return floatval($stack->pop());
    }
}
