An implementation of a Reverse Polish Notation processor

Reverse Polish notation (otherwise known as post-fix, RPN for short) is a way of representing mathematical equations. 
The notation is used because the format that the equation is in is easier for machines to interpret rather than the notation we are used to, infix notation, where the operator is in between the numbers. 
The equation can be complex or simple. RPN doesnt require brackets as the equations are layed out in such a format that it isn't required for machines to understand.
The name RPN is named after Jan Łukasiewicz, a Polish logician who invented Polish notation (prefix notation) some time in the 1920s.

Reverse polish notation should be ordered like this:
<FirstNumber> <SecondNumber> <Operation>

Rather than the normal convention(infix) of:
<FirstNumber> <Operation> <SecondNumber>

Examples:
  
3 4 +
  
3 5 6 + *
  
2 4 / 5 6 - *

references - https://en.wikibooks.org/wiki/A-level_Computing_2009/AQA/Problem_Solving,_Programming,_Operating_Systems,_Databases_and_Networking/Problem_Solving/Reverse_Polish_Notation
