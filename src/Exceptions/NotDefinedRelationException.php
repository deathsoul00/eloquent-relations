<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Exceptions;

use Exception;

class NotDefinedRelationException extends Exception
{
    /**
     * create instance of exception
     *
     * @param string $relation
     * @param string $className
     */
    public function __construct($relation, $className)
    {
        parent::__construct(sprintf('Relation %s was not defined from %s', $relation, $className));
    }
}
