<?php

namespace App\Repository;

use Doctrine\Common\Collections\Expr\Comparison;
use PHPUnit\Framework\TestCase;

class ProduceRepositoryTest extends TestCase
{
	public function testBuildCriteriaFromFiltersWithSingleEqualityFilter(): void
	{
		$filters = ['name_eq' => 'Apples'];

		$criteria = ProduceRepository::buildCriteriaFromFilters($filters);
		$expressions = $criteria->getWhereExpression();

		$this->assertNotNull($expressions);
		$this->assertInstanceOf(Comparison::class, $expressions);
		$this->assertSame('name', $expressions->getField());
		$this->assertSame('=', $expressions->getOperator());
		$this->assertSame('Apples', $expressions->getValue()->getValue());
	}

	public function testBuildCriteriaFromFiltersWithMultipleFilters(): void
	{
		$filters = [
			'type_eq' => 'fruit',
			'weight_gt' => 100,
		];

		$criteria = ProduceRepository::buildCriteriaFromFilters($filters);
		$expressions = $criteria->getWhereExpression();

		$this->assertNotNull($expressions);
		$this->assertInstanceOf(\Doctrine\Common\Collections\Expr\CompositeExpression::class, $expressions);

		$subExpressions = $expressions->getExpressionList();

		$this->assertCount(2, $subExpressions);

		$this->assertSame('type', $subExpressions[0]->getField());
		$this->assertSame('=', $subExpressions[0]->getOperator());
		$this->assertSame('fruit', $subExpressions[0]->getValue()->getValue());

		$this->assertSame('weight', $subExpressions[1]->getField());
		$this->assertSame('>', $subExpressions[1]->getOperator());
		$this->assertSame(100, $subExpressions[1]->getValue()->getValue());
	}

	public function testBuildCriteriaFromFiltersWithInvalidField(): void
	{
		$filters = ['unknownField_eq' => 'Value'];

		$criteria = ProduceRepository::buildCriteriaFromFilters($filters);

		$expressions = $criteria->getWhereExpression();

		$this->assertNull($expressions);
	}

	public function testBuildCriteriaFromFiltersWithEmptyFilters(): void
	{
		$filters = [];

		$criteria = ProduceRepository::buildCriteriaFromFilters($filters);

		$this->assertNull($criteria->getWhereExpression());
	}
}