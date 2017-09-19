<?php

namespace Dot\Users\Models;

/**
 * Class RestQueryBuilder
 */
class RestQueryBuilder
{

    /**
     * logic operarors
     * @var array
     */
    public $logic_operators = [
        '$and' => "and",
        '$or' => "or"
    ];

    /**
     * Arth operators
     * @var array
     */
    public $arth_operators = [
        '$eq' => "=",
        '$gt' => ">",
        '$gte' => ">=",
        '$lt' => "<",
        '$lte' => "<=",
        '$ne' => "!=",
        '$lk' => "like",
        '$in' => "in"
    ];

    /**
     * @var array
     */
    public $operators = [];

    /**
     * @var array
     */
    public $query = [];


    /**
     * RestQueryBuilder constructor.
     * @param array $query
     */
    function __construct($query = [])
    {
        $this->operators = array_merge($this->logic_operators, $this->arth_operators);
        $this->query = $query;
    }

    /**
     * @param null $operator
     * @return bool
     */
    function isOperator($operator = NULL)
    {
        return array_key_exists($operator, $this->operators);
    }

    /**
     * @param null $operator
     * @return bool
     */
    function isLogicOperator($operator = NULL)
    {
        return array_key_exists($operator, $this->logic_operators);
    }

    /**
     * @param null $operator
     * @return bool
     */
    function isArthOperator($operator = NULL)
    {
        return array_key_exists($operator, $this->arth_operators);
    }

    /**
     * @param $operator
     * @return null
     */
    function decodeOperator($operator)
    {

        if (array_key_exists($operator, $this->operators)) {
            return $this->operators[$operator];
        }

        return null;

    }


    function build($query, $rest_query = NULL)
    {

        if ($rest_query) {
            $current_query = $rest_query;
        } else {
            $current_query = $this->query;
        }

        foreach ($current_query as $key => $value) {

            // check if its an operator


            if ($this->isOperator($key)) {

                $this->doWhere($query, $key, $value);

            } else {

                // key is field name
                // check if have and array have operator

                if (is_array($value)) {

                } else {

                }

                $query->where($key, $value);
            }
        }
    }

    function doWhere($query, $key, $value)
    {

        // key is logic operator
        if ($this->isLogicOperator($key)) {

            $operator = $this->decodeOperator($key);

            $query->where(function ($query) use ($value, $operator) {

                if (in_array($operator, ["and", "or"])) {

                    if (count($value)) {

                        foreach ($value as $k => $v) {

                            if (!is_array($v)) {

                                // value is not an array
                                if ($operator == "and") {
                                    $query->where($k, $v);
                                } else {
                                    $query->orWhere($k, $v);
                                }

                            } else {

                                // Value is an array

                                //dd($v);

                                //if ($this->isMultiDimesional($v)) {


                                if (count($v)) {

                                    // check if there is an operator
                                    foreach ($v as $key2 => $value2) {


                                        //dd($key2);

                                        if ($this->isArthOperator($key2)) {

                                            $key2 = $this->decodeOperator($key2);

                                            if ($key2 == "in") {

                                                $query->whereIn($k, (array)$value2);

                                            } else {

                                                if ($operator == "and") {
                                                    $query->where($k, $key2, $value2);
                                                } else {
                                                    $query->orWhere($k, $key2, $value2);
                                                }

                                            }

                                        }

                                        // $this->doWhere($query, $key2, $value2);

                                    }

                                }
                                //}

                            }

                        }

                    }


                }
            });

        }

    }


    function isMultiDimesional($array)
    {
        rsort($array);
        return isset($array[0]) && is_array($array[0]);
    }


}