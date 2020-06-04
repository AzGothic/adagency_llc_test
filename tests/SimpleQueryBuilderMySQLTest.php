<?php declare(strict_types=1);

use app\base\db\SimpleQueryBuilderInterface;
use app\base\db\SimpleQueryBuilderMySQL;
use PHPUnit\Framework\TestCase;

class SimpleQueryBuilderMySQLTest extends TestCase
{
    protected $builder;

    protected function setUp(): void
    {
        $this->builder = new SimpleQueryBuilderMySQL();
    }

    public function testSelectMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->select('id')
        );
    }

    public function testFromMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->from('table')
        );
    }

    public function testWhereMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->where(['id', '1', '>'])
        );
    }

    public function testGroupByMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->groupBy('id')
        );
    }

    public function testHavingMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->having(['id', '3', '<'])
        );
    }

    public function testOrderByMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->orderBy(['id' => 'DESC'])
        );
    }

    public function testLimitMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->limit(10)
        );
    }

    public function testOffsetMustReturnInstanceOfSimpleQueryBuilderInterface(): void
    {
        $this->assertInstanceOf(
            SimpleQueryBuilderInterface::class,
            $this->builder->offset(0)
        );
    }

    public function testBuildAndBuildCountMustReturnString(): void
    {
        $this->assertIsString(
            $this->builder->from('table')->build()
        );
        $this->assertIsString(
            $this->builder->from('table')->buildCount()
        );
    }

    public function testWrongFormatSelectMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->select([[]])
            ->build();
    }

    public function testUsageBuildWithoutFromMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->build();
    }

    public function testWrongFormatFromMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from(1)
            ->build();
    }

    public function testWrongFormatWhereMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->where(['id' => 1])
            ->build();
    }

    public function testWrongFormatGroupByMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->groupBy(['id' => 1])
            ->build();
    }

    public function testUsageHavingWithoutGroupByMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->having(['id', 1])
            ->build();
    }

    public function testWrongFormatHavingMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->groupBy('id')
            ->having(['id' => 1])
            ->build();
    }

    public function testWrongFormatOrderByMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->orderBy([[]])
            ->build();
    }

    public function testWrongFormatLimitMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->limit(-1)
            ->build();
    }

    public function testWrongTypeLimitMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->limit('1')
            ->build();
    }

    public function testUsageOffsetWithoutLimitMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->offset(1)
            ->build();
    }

    public function testWrongFormatOffsetMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->limit(1)
            ->offset([])
            ->build();
    }

    public function testWrongTypeOffsetMustThrowLogicExceptionOnBuild(): void
    {
        $this->expectException(\app\base\LogicException::class);
        $this->builder->from('table')
            ->limit(1)
            ->offset('1')
            ->build();
    }

    public function testBuildQueryCorrect(): void
    {
        $this->assertSame(
            'SELECT field1, field2 AS f2, field3 FROM table1, table2 AS t2, table3 WHERE field1 = 1 AND field2 != 2 AND field3 LIKE "%search%" GROUP BY field1, field2, field3 HAVING field1 = 1 AND field2 != 2 AND field3 LIKE "%search%" ORDER BY field1 DESC, field2, field3 DESC LIMIT 100 OFFSET 10',
            $this->builder
                ->select(['field1', 'f2' => 'field2'])
                ->select('field3')
                ->from('table1')
                ->from(['t2' => 'table2', 'table3'])
                ->where(['field1', 1])
                ->where(['field2', 2, '!='])
                ->where(['field3', '"%search%"', 'LIKE'])
                ->groupBy('field1, field2')
                ->groupBy('field3')
                ->having(['field1', 1])
                ->having(['field2', 2, '!='])
                ->having(['field3', '"%search%"', 'LIKE'])
                ->orderBy('field1 DESC, field2')
                ->orderBy(['field3' => 'DESC'])
                ->limit(100)
                ->offset(10)
                ->build()
        );
    }

    public function testBuildCountQueryCorrect(): void
    {
        $this->assertSame(
            'SELECT COUNT(*) FROM table1, table2 AS t2, table3 WHERE field1 = 1 AND field2 != 2 AND field3 LIKE "%search%" GROUP BY field1, field2, field3 HAVING field1 = 1 AND field2 != 2 AND field3 LIKE "%search%"',
            $this->builder
                ->select(['field1', 'f2' => 'field2'])
                ->select('field3')
                ->from('table1')
                ->from(['t2' => 'table2', 'table3'])
                ->where(['field1', 1])
                ->where(['field2', 2, '!='])
                ->where(['field3', '"%search%"', 'LIKE'])
                ->groupBy('field1, field2')
                ->groupBy('field3')
                ->having(['field1', 1])
                ->having(['field2', 2, '!='])
                ->having(['field3', '"%search%"', 'LIKE'])
                ->orderBy('field1 DESC, field2')
                ->orderBy(['field3' => 'DESC'])
                ->limit(100)
                ->offset(10)
                ->buildCount()
        );
    }
}
