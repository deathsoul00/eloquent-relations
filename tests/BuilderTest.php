<?php
/**
 * @author Mike Alvarez <michaeljpalvarez@gmail.com>
 */

namespace Deathsoul\Eloquent\Tests;

use Deathsoul\Eloquent\Builder;
use Deathsoul\Eloquent\Tests\Models\Foo;

class BuilderTest extends TestCase
{
    /**
     * test simple single join relations
     *
     * @return void
     */
    public function testSimpleJoinRelations()
    {
        // create the builder instance
        $builder = app(Builder::class);
        $builder->setModel(new Foo([]));

        // call a bar relation
        $builder->joinRelations('bar');

        // this should be equal value of the generated sql
        $expectedSql = 'select foos.* from "foos" left join "bars" as "bars" on "bars"."foo_id" = "foos"."id" group by "foos"."id"';

        // assert
        $this->assertEquals($expectedSql, $builder->toSql());
    }
}
