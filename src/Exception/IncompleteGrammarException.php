<?php

namespace Xylemical\Parser\Exception;

use Xylemical\Token\Exception\TokenException;

/**
 * Triggers when not all tokens have been lexed.
 */
class IncompleteGrammarException extends TokenException {

}
