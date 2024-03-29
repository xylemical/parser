# Parser

Convert strings into an abstract syntax trees, and abstract syntax trees into strings.

## Install

The recommended way to install this library is [through composer](http://getcomposer.org).

```sh
composer require xylemical/parser
```

## Usage

### Tokenizer

The primary usage of the tokenizer is:

```php
<?php

use Xylemical\Parser\Tokenizer;
use Xylemical\Parser\Parser;

$tokenizer = (new Tokenizer())->setPatterns([
  'word' => '\w+',
  'whitespace' => '[ \t]+',
]);

$result = $tokenizer->tokenize('This is a test scenario');
```

#### Refinements

Refinements allow the tokenizer to be better at classifying tokens by refining them.

An example of this would be a programming language, that specifies keywords being the `[a-zA-z]+` regex pattern, but
then the individual tokens being `if`, `then`, etc. By specifying the refinements of the `keyword` token, it will
automatically convert these tokens, while defaulting back to `keyword` when none of the refinement patterns match.

```php
<?php

use Xylemical\Parser\Tokenizer;

$tokenizer = (new Tokenizer())->setPatterns([
  'word' => '\w+',
  'whitespace' => '[ \t]+',
])->setRefinements('word', [
    'keyword' => '^[a-z]+$',
  ])->setRefinements('keyword', [
    'if' => 'if',
    'then' => 'then',
  ]);

$stream = $tokenizer->tokenize('if this1 then that');

// The stream would have the following tokens:
// * Token('if', 'if', 1, 1)
// * Token('whitespace', ' ', 1, 3)
// * Token('word', 'this1', 1, 4)
// * Token('whitespace', ' ', 1, 9)
// * Token('then', 'then', 1, 10)
// * Token('whitespace', ' ', 1, 14)
// * Token('keyword', 'that', 1, 15)
```

#### Subclass of Tokenizer

Subclassing the tokenizer allows for the definition of default tokens and refinements.

```php
<?php

namespace Example;

use Xylemical\Parser\Tokenizer as BaseTokenizer;

class Tokenizer extends BaseTokenizer {

  protected const PATTERNS = [
    'word' => '\w+',
    'number' => '-?\d+(?\.\d+)',
  ];

  protected const REFINEMENTS = [
    'number' => [
      'float' => '^-?\d+\.\d+$',
    ],
  ];

}
```

### Lexer



```

```

### Subclass of Lexer

Subclassing the lexer is important for generating something from the TokenStream generated by the tokenizer.

For example, the following lexer when discovering a `word` token, would generate a `word`, `word|number|float` pair:

```php
<?php
namespace Example;

use Xylemical\Parser\Lexer as BaseLexer;

class Lexer extends BaseLexer {

  public function generate(TokenStream $stream): mixed {
    if ($stream->is('word')) {
      $word = $stream->expect('word');
      $stream->expect('equals');
      $result = $stream->expectOneOf(['word', 'number', 'float']);
      return [$word, $result];
    }
    return NULL;
  }

}
```

## License

MIT, see LICENSE.
