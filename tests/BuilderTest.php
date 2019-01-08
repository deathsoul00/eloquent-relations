<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Tests;

use Deathsoul\Eloquent\Builder;
use Deathsoul\Eloquent\Tests\Models\Foo;
use Deathsoul\Eloquent\Tests\Models\Bar;

class BuilderTest extends TestCase
{
    /**
     * test simple single join relations (one-to-one)
     *
     * @return void
     */
    public function testSimpleJoinRelationsOneToOne()
    {
        // create the builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call a bar relation
        $builder->joinRelations('bar');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "bars" as "bars" on "bars"."foo_id" = "foos"."id" group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test multiple relation join clause (one-to-one)
     *
     * @return void
     */
    public function testMultipleJoinRelationsOneToOne()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('bar')
            ->joinRelations('crate');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "bars" as "bars" on "bars"."foo_id" = "foos"."id" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test simple single join relations (one-to-many)
     *
     * @return void
     */
    public function testSingleJoinRelationsOneToMany()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('bars');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "bars" as "bars" on "bars"."foo_id" = "foos"."id" group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test multiple relation join clause (one-to-many)
     *
     * @return void
     */
    public function testMultipleJoinRelationsOneToMany()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('bars')
            ->joinRelations('crates');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "bars" as "bars" on "bars"."foo_id" = "foos"."id" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test inverse relation join clause
     *
     * @return void
     */
    public function testInverseJoinRelation()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Bar([]));

        // call method
        $builder->joinRelations('foo');

        // this should be equal value of the generated sql
        $expectedSql = 'select "bars".* from "bars" left join "foos" as "foos" on "foos"."id" = "bars"."foo_id" group by "bars"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test complex join relation
     *
     * @return void
     */
    public function testComplexJoinRelation()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('crate.apples');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" left join "apples" as "apples" on "apples"."crate_id" = "crates"."id" group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test relation with delete without trashed scope
     *
     * @return void
     */
    public function testRelationWithoutTrashedScope()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('untrashedCrates');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" and "crates"."deleted_at" is null group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test relation with delete trashed scope
     *
     * @return void
     */
    public function testRelationOnlyTrashedScope()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('trashedCrates');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" and "crates"."deleted_at" is not null group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test relation where scope
     *
     * @return void
     */
    public function testRelationWhereScope()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('activeCrates');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" and "crates"."status" = ? group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test relation orWhere scope
     *
     * @return void
     */
    public function testRelationOrWhereScope()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->joinRelations('someCrates');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" or "crates"."status" = ? group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test where join clause that creates a where clause for the selected relation
     *
     * @return void
     */
    public function testWhereJoinClause()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->whereJoin('crates.status', 'A');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" where "crates"."status" = ? group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test where join clause that creates a where clause for the selected relation using or boolean
     *
     * @return void
     */
    public function testOrWhereJoinClause()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder
            ->where('status', 'A')
            ->orWhereJoin('crates.status', 'A');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".* from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" where "status" = ? or "crates"."status" = ? group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test order by join clause with will create an order clause for selected relation join
     *
     * @return void
     */
    public function testOrderByJoinClause()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->orderByJoin('crates.name', 'ASC');

        // this should be equal value of the generated sql
        $expectedSql = 'select "foos".*, MAX(crates.name) as sort from "foos" left join "crates" as "crates" on "crates"."foo_id" = "foos"."id" group by "foos"."id" order by sort ASC';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }

    /**
     * test thrown exception if given aggregate method is not valid
     *
     * @expectedException \InvalidArgumentException
     *
     * @return void
     */
    public function testThrownExceptionForInvalidAggregateMethod()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call method
        $builder->orderByJoin('crates.name', 'ASC', 'DB_FOUND_ROWS');
    }

    /**
     * test thrown exception if given relation does not supported by the library
     *
     * @expectedException \Deathsoul\Eloquent\Exceptions\UnsupportedRelationException
     *
     * @return void
     */
    public function testThrownExceptionForUnsupportedRelation()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        $builder->joinRelations('appleCrates');
    }

    /**
     * test thrown exception if given relation was not defined in the model
     *
     * @expectedException \Deathsoul\Eloquent\Exceptions\NotDefinedRelationException
     *
     * @return void
     */
    public function testThrownExceptionForUndefinedRelation()
    {
        // create new builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        $builder->joinRelations('bananas');
    }
}
