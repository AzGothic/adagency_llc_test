<?php
declare(strict_types=1);

namespace app\base\db;

use app\helper\arrayHelper;
use app\base\LogicException;

abstract class SimpleQueryBuilderAbstract implements SimpleQueryBuilderInterface
{
    /** @var array $select */
    protected $select = [];

    /** @var array $from */
    protected $from = [];

    /** @var array $where */
    protected $where = [];

    /** @var array $groupBy */
    protected $groupBy = [];

    /** @var array $having */
    protected $having = [];

    /** @var array $orderBy */
    protected $orderBy = [];

    /** @var integer $limit */
    protected $limit = 0;

    /** @var integer $offset */
    protected $offset = 0;


    /**
     * Format:
     * string, example, 'field1, field2, field3 AS f3'
     * array, example, ['field1', 'field2', 'f3' => 'field3']
     *      'key => value' definition is "value AS key"
     *
     * @param array|string $fields
     * @return SimpleQueryBuilderInterface
     */
    public function select($fields): SimpleQueryBuilderInterface
    {
        if (is_string($fields)) {
            $this->select[] = trim($fields, ',');
        }
        elseif (is_array($fields)) {
            $this->select = arrayHelper::merge($this->select, $fields);
        }

        return $this;
    }

    /**
     * Format:
     * string, example, 'table1, table2, table3 AS t3'
     * array, example, ['table1', 'table2', 't3' => 'table3']
     *      'key => value' definition is "value AS key"
     * SimpleQueryBuilderInterface, will use ->build() method to get sub query
     *
     * @param string|SimpleQueryBuilderInterface|array<string|SimpleQueryBuilderInterface> $tables
     * @return SimpleQueryBuilderInterface
     */
    public function from($tables): SimpleQueryBuilderInterface
    {
        if (is_string($tables) || $tables instanceof SimpleQueryBuilderInterface) {
            $this->from[] = trim($tables, ',');
        }
        elseif (is_array($tables)) {
            $this->from = arrayHelper::merge($this->from, $tables);
        }

        return $this;
    }

    /**
     * Format:
     * string, example, 'field1 = 1 AND field2 != 2 AND text LIKE "%search%"'
     * array, one where condition per one method call in format [field, value, operator] - operator is not required, default value is '='
     *      examples, ['field1', 1]
     *                ['field2', 2, '!=']
     *                ['text', '%search%', 'LIKE']
     *
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function where($conditions): SimpleQueryBuilderInterface
    {
        if (is_string($conditions)) {
            $this->where[] = trim($conditions);
        }
        elseif (is_array($conditions)) {
            $this->where[] = $conditions;
        }

        return $this;
    }

    /**
     * Format:
     * string, example, 'field1, field2'
     * array, example, ['field1', 'field2']
     *
     * @param string|array $fields
     * @return SimpleQueryBuilderInterface
     */
    public function groupBy($fields): SimpleQueryBuilderInterface
    {
        if (is_string($fields)) {
            $this->groupBy[] = trim($fields, ',');
        }
        elseif (is_array($fields)) {
            $this->groupBy = arrayHelper::merge($this->groupBy, $fields);
        }

        return $this;
    }

    /**
     * Format:
     * look at 'where()' method
     *
     * @param string|array $conditions
     * @return SimpleQueryBuilderInterface
     */
    public function having($conditions): SimpleQueryBuilderInterface
    {
        if (is_string($conditions)) {
            $this->having[] = trim($conditions);
        }
        elseif (is_array($conditions)) {
            $this->having[] = $conditions;
        }

        return $this;
    }

    /**
     * Format:
     * string, example, 'field1 ASC, field2 DESC'
     * array, example, ['field1', 'field2' => 'DESC'], default sort order is ASC
     *
     * @param string|array $fields
     * @return SimpleQueryBuilderInterface
     */
    public function orderBy($fields): SimpleQueryBuilderInterface
    {
        if (is_string($fields)) {
            $this->orderBy[] = trim($fields, ',');
        }
        elseif (is_array($fields)) {
            $this->orderBy = arrayHelper::merge($this->orderBy, $fields);
        }

        return $this;
    }

    /**
     * @param int $limit
     * @return SimpleQueryBuilderInterface
     */
    public function limit($limit): SimpleQueryBuilderInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param int $offset
     * @return SimpleQueryBuilderInterface
     */
    public function offset($offset): SimpleQueryBuilderInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @throws LogicException
     * @return string
     */
    public function build(): string
    {
        $query = '';

        /** SELECT processing */
        $query .= 'SELECT ';
        if (empty($this->select)) {
            $query .= '*';
        }
        else {
            foreach ($this->select as $key => $value) {
                if (!is_string($value) && !is_numeric($value)) {
                    throw new LogicException('Wrong format for "SELECT" value: ' . print_r($value, true));
                }

                $query .= $value;
                if (is_string($key)) {
                    $query .= ' AS ' . $key;
                }
                $query .= ', ';
            }
            $query = rtrim($query, ', ');
        }

        /** FROM processing */
        $query .= ' FROM ';
        if (empty($this->from)) {
            throw new LogicException('"FROM" can`t be empty');
        }
        else {
            foreach ($this->from as $key => $value) {
                if (!is_string($value) && !($value instanceof SimpleQueryBuilderInterface)) {
                    throw new LogicException('Wrong format for "FROM" value: ' . print_r($value, true));
                }

                if ($value instanceof SimpleQueryBuilderInterface) {
                    $query .= $value->build();
                }
                else {
                    $query .= $value;
                }

                if (is_string($key)) {
                    $query .= ' AS ' . $key;
                }
                $query .= ', ';
            }
            $query = rtrim($query, ', ');
        }

        /** WHERE processing */
        if (!empty($this->where)) {
            $query .= ' WHERE ';
            $query .= $this->prepareWhere($this->where);
        }

        /** GROUP BY processing */
        if (!empty($this->groupBy)) {
            $query .= ' GROUP BY ';
            foreach ($this->groupBy as $value) {
                if (!is_string($value)) {
                    throw new LogicException('Wrong format for "GROUP BY" value: ' . print_r($value, true));
                }

                $query .= $value;
                $query .= ', ';
            }
            $query = rtrim($query, ', ');
        }

        /** HAVING processing */
        if (!empty($this->having)) {
            if (empty($this->groupBy)) {
                throw new LogicException('"HAVING" can`t be used without "GROUP BY" condition');
            }
            $query .= ' HAVING ';
            $query .= $this->prepareWhere($this->having);
        }

        /** ORDER BY processing */
        if (!empty($this->orderBy)) {
            $query .= ' ORDER BY ';
            foreach ($this->orderBy as $key => $value) {
                $field = $value;
                $order = 'ASC';
                if (is_string($key)) {
                    $field = $key;
                    $order = strtoupper($value);
                }

                if (!is_string($field)) {
                    throw new LogicException('Wrong format for "ORDER BY" value: ' . print_r($field, true));
                }

                if (!in_array($order, ['ASC', 'DESC'])) {
                    throw new LogicException('Wrong value for "ORDER BY" sort order: ' . print_r($order, true));
                }

                if (is_string($key)) {
                    $field .= ' ' . $order;
                }

                $query .= $field;
                $query .= ', ';
            }
            $query = rtrim($query, ', ');
        }

        /** LIMIT and OFFSET processing */
        if ($this->limit) {
            $query .= ' LIMIT ';
            if (!is_integer($this->limit) || $this->limit < 0) {
                throw new LogicException('Wrong format for "LIMIT" value: ' . print_r($this->limit, true));
            }
            $query .= $this->limit;

            $query .= ' OFFSET ';
            if (!is_integer($this->offset)) {
                throw new LogicException('Wrong format for "OFFSET" value: ' . print_r($this->offset, true));
            }
            $query .= $this->offset;
        }
        elseif ($this->offset) {
            throw new LogicException('"OFFSET" can`t be used without "LIMIT" condition');
        }

        /** Return query */
        return $query;
    }

    /**
     * @throws LogicException
     * @return string
     */
    public function buildCount(): string
    {
        $selectTmp = $this->select;
        $this->select = [];
        $this->select('COUNT(*)');

        $orderByTmp = $this->orderBy;
        $this->orderBy = [];

        $limitTmp = $this->limit;
        $this->limit = 0;

        $offsetTmp = $this->offset;
        $this->offset = 0;

        $query = $this->build();

        $this->select  = $selectTmp;
        $this->orderBy = $orderByTmp;
        $this->limit   = $limitTmp;
        $this->offset  = $offsetTmp;

        return $query;
    }



    /**
     * @param array $where
     * @throws LogicException
     * @return string
     */
    public function prepareWhere($where): string
    {
        if (!$where) {
            return '';
        }
        $whereConditions = [];
        foreach ($where as $value) {
            if (is_string($value)) {
                $whereConditions[] = $value;
            }
            elseif (is_array($value)) {
                if (empty($value[0]) || !isset($value[1])) {
                    throw new LogicException('Wrong format for "WHERE" array: ' . print_r($value, true));
                }

                if (!is_string($value[0]) && !is_numeric($value[0])) {
                    throw new LogicException('Wrong format for "WHERE" key: ' . print_r($value[0], true));
                }

                if (!is_string($value[1]) && !is_numeric($value[1])) {
                    throw new LogicException('Wrong format for "WHERE" value: ' . print_r($value[1], true));
                }

                $operator = !empty($value[2]) ? $value[2] : '=';
                if (!is_string($operator)) {
                    throw new LogicException('Wrong format for "WHERE" operator: ' . print_r($operator, true));
                }
                $whereConditions[] = $value[0] . ' ' . $operator . ' ' . $value[1];
            }
            else {
                throw new LogicException('Wrong format for "WHERE" array: ' . print_r($value, true));
            }
        }

        return implode(' AND ', $whereConditions);
    }
}
