<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Concerns;

trait Relations
{
    /**
     * call methods for this class
     *
     * @param  string $method
     * @param  array  $parameters
     *
     * @return void
     */
    public function __call($method, $parameters)
    {
        $this->getQuery()->relationsClauses[] = [$method => $parameters];
        return parent::__call($method, $parameters);
    }
}
