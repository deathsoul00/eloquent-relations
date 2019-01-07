<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent;

use Closure;
use Deathsoul\Eloquent\Relations\HasOne;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Database\Eloquent\ScopeDeletingScope;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Deathsoul\Eloquent\Exceptions\NotDefinedRelationException;
use Deathsoul\Eloquent\Exceptions\UnsupportedRelationException;

class Builder extends EloquentBuilder
{
    /**
     * sql aggregate methods
     *
     * @const
     */
    const AGGREGATE_MIN = 'MIX';
    const AGGREGATE_MAX = 'MAX';
    const AGGREGATE_AVG = 'AVG';
    const AGGREGATE_SUM = 'SUM';
    const AGGREGATE_COUNT = 'COUNT';

    /**
     * eloquent builder instance
     *
     * @var \Illuminate\Database\Eloquent\Builder
     */
    public $builder;

    /**
     * query relation clauses
     *
     * @var array
     */
    public $relationClauses = [];

    /**
     * {@inheritDoc}
     *
     * @param string      $column
     * @param string|null $operator
     * @param mixed|null  $value
     * @param string      $boolean
     *
     * @return self
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        if ($column instanceof Closure) {
            $query = $this->getModel()->newModelQuery();
            $query->builder = $this->builder ?: $this;

            // call callback
            $column($query);

            // create nested query (where)
            $this->query->addNestedWhereQuery($query->getQuery(), $boolean);
        } else {
            call_user_func_array([$this->query, 'where'], func_get_args());
        }

        return $this;
    }

    /**
     * create a where clause for join statement
     *
     * @param  string $column
     * @param  string $operator
     * @param  mixed  $value
     * @param  string $boolean
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function whereJoin($column, $operator = null, $value = null, $boolean = 'and')
    {
        $query = ($this->builder) ?: $this;
        $column = $query->performJoin($column);

        return call_user_func_array([$this, 'where'], func_get_args());
    }

    /**
     * create a where clause for join using OR boolean
     *
     * @param  string $column
     * @param  string $operator
     * @param  mixed  $value
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orWhereJoin($column, $operator = null, $value = null)
    {
        $query = ($this->builder) ?: $this;
        $column = $query->performJoin($column);

        return call_user_func_array([$this, 'orWhere'], func_get_args());
    }

    /**
     * create order by clause for the join clause
     *
     * @param  string $column
     * @param  string $direction
     * @param  string $aggregateMethod
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function orderByJoin($column, $direction = 'asc', $aggregateMethod = null)
    {
        $query = ($this->builder) ?: $this;
        $column = $query->performJoin($column);

        if (strrpos($column, '.') !== false) {
            $aggregateMethod = ($aggregateMethod) ?: $this->aggregateMethod;
            if ($this->checkAggregateMethod($aggregateMethod)) {
                $query->selectRaw("{$aggregateMethod}.({$column}) as sort");
                return $query->orderRaw("sort {$direction}");
            }
        }

        return $this->orderBy($column, $direction);
    }

    /**
     * performs a join clause for the given relation
     *
     * @param  string  $relations
     * @param  bool    $leftJoin
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function joinRelations($relations, $leftJoin = true)
    {
        $query = ($this->builder) ?: $this;
        $column = $query->performJoin("{$relations}.FAKE_FIELD", $leftJoin);

        return $this;
    }

    protected function performJoin($relations, $leftJoin = true)
    {
        // get what join method to be used
        $joinMethod = ($leftJoin === true) ? 'leftJoin' : 'join';

        // parse relations
        $args = explode('.', $relations);
        $column = end($args);
        $currentModel = $baseModel = $this->getModel();
        $currentBaseTable = $currentTableAlias = $baseTable = $baseModel->getTable();
        $currentPrimaryKey = $baseTablePrimaryKey = $baseModel->getKeyName();

        static $joinedTables = [];
        static $selected = false;

        $relatedJoins = [];
        foreach ($args as $arg) {
            if ($arg === $column) {
                // we have reached the end of the relation
                break;
            }

            if (! is_callable([$currentModel, $arg])) {
                throw new NotDefinedRelationException($arg, get_class($currentModel));
            }

            $relation = $currentModel->$arg();
            $relatedModel = $relation->getModel();
            $relatedTable = $relatedModel->getTable();
            $relatedPrimaryKey = $relatedModel->getKeyName();
            $relatedTableAlias = $relatedTable;

            $relatedJoins[] = $relatedTableAlias;
            $relatedJoinString = implode('_', $relatedJoins);

            if (! in_array($relatedJoinString, $joinedTables)) {
                $joinClause = "{$relatedTable} as {$relatedTableAlias}";

                if ($relation instanceof HasOne) {
                    $relatedKey = $relation->getQualifiedForeignKeyName();
                    $relatedKey = last(explode('.', $relatedKey));
                    $this->$joinMethod($joinClause, function ($join) use ($relation, $relatedTableAlias, $relatedKey, $currentTableAlias, $currentPrimaryKey) {
                        $join->on("{$relatedTableAlias}.{$relatedKey}", '=', "{$currentTableAlias}.{$currentPrimaryKey}");
                        $this->joinQuery($join, $relation, $relatedTableAlias);
                    });
                } else {
                    throw new UnsupportedRelationException($relation);
                }

                $currentModel = $relatedModel;
                $currentTableAlias = $relatedTableAlias;
                $currentPrimaryKey = $relatedPrimaryKey;

                // cache already joined tables
                $joinedTables[] = $relatedJoinString;
            }
        }

        if ($selected === false && count($args) > 1) {
            $selected = true;
            $this->selectRaw("{$baseTable}.*");
            $this->groupBy("{$baseTable}.{$baseTablePrimaryKey}");
        }

        return "{$currentTableAlias}.{$column}";
    }

    /**
     * applies join query clauses to relations
     *
     * @param  \Illuminate\Database\Query\JoinClause            $join
     * @param  \Illuminate\Database\Eloquent\Relations\Relation $relation
     * @param [type] $relatedTableAlias
     * @return void
     */
    protected function joinQuery(JoinClause $join, Relation $relation, $relatedTableAlias)
    {
        /** @var \Illuminate\Database\Query\Builder */
        $builder = $relation->getQuery();

        if (isset($builder->relationClauses)) {
            foreach ($builder->relationClauses as $clause) {
                foreach ($clause as $method => $params) {
                    $this->applyClauseOnRelation($join, $relation, $method, $params, $relatedTableAlias);
                }
            }
        }

        foreach ($builder->scopes as $scope) {
            if ($scope instanceof ScopeDeletingScope) {
                $this->applyClauseOnRelation($join, $relation, 'withoutTrashed', $params, $relatedTableAlias);
            }
        }
    }

    /**
     * applying clauses on the relation query builder
     *
     * @param  \Illuminate\Database\Query\JoinClause            $join
     * @param  \Illuminate\Database\Eloquent\Relations\Relation $relation
     * @param  string                                           $method
     * @param  array                                            $params
     * @param  string                                           $relatedTableAlias
     *
     * @return void
     */
    protected function applyClauseOnRelation(JoinClause $join, Relation $relation, $method, array $params, $relatedTableAlias)
    {
        if (in_array($method, ['where', 'orWhere'])) {
            if (is_array($params[0])) {
                foreach ($params[0] as $k => $param) {
                    $params[0]["{$relatedTableAlias}.{$key}"] = $param;
                    unset($params[0][$k]);
                }
            } else {
                $params[0]["{$relatedTableAlias}.{$key}"] = $param;
            }

            call_user_func_array([$join, $method], $params);
        } elseif (in_array($method, ['withoutTrashed', 'onlyTrashed', 'withTrashed'])) {
            if ($method == 'withoutTrashed') {
                call_user_func_array([$join, 'where'], ["{$relatedTableAlias}.{$relation->getModel()->getDeletedAtColumn()}", '=', null]);
            } elseif ($method == 'onlyTrashed') {
                call_user_func_array([$join, 'where'], ["{$relatedTableAlias}.{$relation->getModel()->getDeletedAtColumn()}", '<>', null]);
            }
        }
    }

    /**
     * checks aggregate method used
     *
     * @throws \InvalidArgumentException
     *
     * @param  string $method
     *
     * @return bool
     */
    protected function checkAggregateMethod($method)
    {
        if (! in_array($aggregates = [
            static::AGGREGATE_SUM,
            static::AGGREGATE_AVG,
            static::AGGREGATE_MIN,
            static::AGGREGATE_MAX,
            static::AGGREGATE_COUNT
        ], $method)
        ) {
            throw new InvalidArgumentException(
                sprintf('Aggregate method used is not valid, you may use %s', implode(',', $aggregates))
            );
        }

        return true;
    }
}
