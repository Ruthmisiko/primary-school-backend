<?php

namespace App\Repositories;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Container\Container as Application;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @param Application $app
     *
     * @throws \Exception
     */
    protected $request;

    public function __construct(Application $app,Request $request)
    {
        $this->request = $request;
        $this->app = $app;
        $this->makeModel();
    }

    /**
     * Get searchable fields array
     *
     * @return array
     */
    abstract public function getFieldsSearchable();

    /**
     * Configure the Model
     *
     * @return string
     */
    abstract public function model();

    /**
     * Make Model instance
     *
     * @throws \Exception
     *
     * @return Model
     */
    public function makeModel()
    {
        $model = $this->app->make($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }

    /**
     * Paginate records for scaffold.
     *
     * @param int $perPage
     * @param array $columns
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($perPage, $columns = ['*'])
    {
        $query = $this->allQuery();

        return $query->paginate($perPage, $columns);
    }

    /**
     * Build a query for retrieving all records.
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function allQuery()
    {
        $query = $this->model->newQuery();
        $date_range=explode(',',request('date_range',null));
        $fieldsSearchable = $this->getFieldsSearchable();
        $search = $this->request->get( 'search', null);
        $searchFields = $this->request->get('searchFields', null);
        $filter = $this->request->get('filter', null);
        $orderBy = $this->request->get('orderBy', null);
        $sortedBy = $this->request->get('sortedBy', 'asc');
        $with = $this->request->get('with', null);
        $withCount = $this->request->get('withCount', null);
        $searchJoin = $this->request->get('searchJoin', null);


        if ($search && is_array($fieldsSearchable) && count($fieldsSearchable)) {


            $searchFields = is_array($searchFields) || is_null($searchFields) ? $searchFields : explode(';', $searchFields);
            $fields = $this->parserFieldsSearch($fieldsSearchable, $searchFields);
            $isFirstField = true;
            $searchData = $this->parserSearchData($search);
            $search = $this->parserSearchValue($search);
            $modelForceAndWhere = strtolower($searchJoin) === 'and';

            $query  = $query ->where(function ($query) use ($fields, $search, $searchData, $isFirstField, $modelForceAndWhere) {

                foreach ($fields as $field => $condition) {

                    if (is_numeric($field)) {
                        $field = $condition;
                        $condition = "=";
                    }

                    $value = null;

                    $condition = trim(strtolower($condition));

                    if (isset($searchData[$field])) {
                        $value = ($condition == "like" || $condition == "ilike") ? "%{$searchData[$field]}%" : $searchData[$field];
                    } else {
                        if (!is_null($search)) {
                            $value = ($condition == "like" || $condition == "ilike") ? "%{$search}%" : $search;
                        }
                    }

                    $relation = null;
                    if(stripos($field, '.')) {
                        $explode = explode('.', $field);
                        $field = array_pop($explode);
                        $relation = implode('.', $explode);
                    }

                    $modelTableName = $query->getModel()->getTable();

                    if ( $isFirstField || $modelForceAndWhere ) {
                        if (!is_null($value)) {
                            if(!is_null($relation)) {
                                $query->whereHas($relation, function($query) use($field,$condition,$value) {
                                    $query->where($field,$condition,$value);
                                });
                            } else {
                                $query->where($modelTableName.'.'.$field,$condition,$value);
                            }
                            $isFirstField = false;
                        }
                    } else {
                        if (!is_null($value)) {
                            if(!is_null($relation)) {
                                $query->orWhereHas($relation, function($query) use($field,$condition,$value) {
                                    $query->where($field,$condition,$value);
                                });
                            } else {
                                $query->orWhere($modelTableName.'.'.$field, $condition, $value);
                            }
                        }
                    }
                }

            });
        }
        if (isset($orderBy) && !empty($orderBy)) {
            $orderBySplit = explode(';', $orderBy);
            if(count($orderBySplit) > 1) {
                $sortedBySplit = explode(';', $sortedBy);
                foreach ($orderBySplit as $orderBySplitItemKey => $orderBySplitItem) {
                    $sortedBy = isset($sortedBySplit[$orderBySplitItemKey]) ? $sortedBySplit[$orderBySplitItemKey] : $sortedBySplit[0];
                    $query  = $this->parserFieldsOrderBy($query , $orderBySplitItem, $sortedBy);
                }
            } else {
                $query  = $this->parserFieldsOrderBy($query , $orderBySplit[0], $sortedBy);
            }
        }

        if (isset($filter) && !empty($filter)) {
            if (is_string($filter)) {
                $filter = explode(';', $filter);
            }

            $query  = $query ->select($filter);
        }

        if ($with) {
            $with = explode(';', $with);
            $query  =$query ->with($with);
        }

        if ($withCount) {
            $withCount = explode(';', $withCount);
            $query = $query ->withCount($withCount);
        }


        //allow system to retrieve roles for a shop different from the one in the header
        if(request()->has('shop_id') && $this->model->table=="roles"){
            $query->where($this->model->table.'.shop_id',request()->get('shop_id'));
        }else{
            if (request()->hasHeader('shop_id') && !empty(request()->header('shop_id')) && in_array('shop_id',$this->getFieldsSearchable())) {
                $query->where($this->model->table.'.shop_id', request()->header('shop_id'));
            }
        }

        return $query;
    }

    public function whereHas($attribute, \Closure $closure = null)
    {
        return $this->model->whereHas($attribute, $closure);
    }
    /**
     * Retrieve all records with given filter criteria
     *
     * @param array $search
     * @param int|null $skip
     * @param int|null $limit
     * @param array $columns
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public function all($search = [], $skip = null, $limit = null, $columns = ['*'])
    {
        $query = $this->allQuery($search, $skip, $limit);

        return $query->get($columns);
    }

    /**
     * Create model record
     *
     * @param array $input
     *
     * @return Model
     */
    public function create($input)
    {
        $model = $this->model->newInstance($input);

        $model->save();

        return $model;
    }

    /**
     * Find model record for given id
     *
     * @param int $id
     * @param array $columns
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model|null
     */
    public function find($id, $columns = ['*'])
    {
        $query = $this->model->newQuery();

        return $query->find($id, $columns);
    }

    /**
     * Update model record for given id
     *
     * @param array $input
     * @param int $id
     *
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Model
     */
    public function update($input, $id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        $model->fill($input);

        $model->save();

        return $model;
    }

    /**
     * @param int $id
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function delete($id)
    {
        $query = $this->model->newQuery();

        $model = $query->findOrFail($id);

        return $model->delete();
    }
    /**
     * @param array $with
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function with($relations)
    {
        $query = $this->allQuery();

        return $query->with($relations);
    }
    /**
     * @param array $when
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function when($bool,$callback)
    {
        $query = $this->allQuery();

        return $query->when($bool,$callback);
    }
    /**
     * @param array $when
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function leftJoin($table,$callback)
    {
        $query = $this->allQuery();

        return $query->leftJoin($table,$callback);
    }
    /**
     * @param array $withSum
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function withSum($table,$column)
    {
        $query = $this->allQuery();

        return $query->withSum($table,$column);
    }
    /**
     * @param array $havingRaw
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function havingRaw($str)
    {
        $query = $this->allQuery();

        return $query->havingRaw($str);
    }
    /**
     * @param array $orderBy
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function orderBy($column,$direction)
    {
        $query = $this->allQuery();

        return $query->orderBy($column,$direction);
    }
    /**
     * @param array $orderBy
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function latest()
    {
        $query = $this->allQuery();

        return $query->latest();
    }
    /**
     * @param array $whereBetween
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function whereDate($column,$condition,$value){
        $query = $this->allQuery();

        return $query->whereDate($column,$condition,$value);
    }
    /**
     * @param array $whereMonth
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function whereMonth($column,$value){
        $query = $this->allQuery();

        return $query->whereMonth($column,$value);
    }

    /**
     * @param array $sum
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function sum($column){
        $query = $this->allQuery();

        return $query->sum($column);
    }
    /**
     * @param array $count
     *
     * @throws \Exception
     *
     * @return int
     */
    public function count(){
        $query = $this->allQuery();

        return $query->count();
    }
    /**
     * @param array $groupBy
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function groupBy($q){
        $query = $this->allQuery();

        return $query->groupBy($q);
    }
    /**
     * @param array $sum
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function selectRaw($q){

        $query = $this->allQuery();
        return $query->selectRaw($q);
    }
    /**
     * @param array $sum
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function where($column,$value){
        $query = $this->allQuery();

        return $query->where($column,$value);
    }

    /**
     * @param array $sum
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function max($column){
        $query = $this->allQuery();

        return $query->max($column);
    }
    /**
     * @param array $select
     *
     * @throws \Exception
     *
     * @return bool|mixed|null
     */
    public function select($q){
        $query = $this->allQuery();

        return $query->select($q);
    }
    /**
     * @param $model
     * @param $orderBy
     * @param $sortedBy
     * @return mixed
     */
    protected function parserFieldsOrderBy($model, $orderBy, $sortedBy)
    {
        $split = explode('|', $orderBy);
        if(count($split) > 1) {
            /*
             * ex.
             * products|description -> join products on current_table.product_id = products.id order by description
             *
             * products:custom_id|products.description -> join products on current_table.custom_id = products.id order
             * by products.description (in case both tables have same column name)
             */
            $table = $model->getModel()->getTable();
            $sortTable = $split[0];
            $sortColumn = $split[1];

            $split = explode(':', $sortTable);
            if(count($split) > 1) {
                $sortTable = $split[0];
                $keyName = $table.'.'.$split[1];
            } else {
                /*
                 * If you do not define which column to use as a joining column on current table, it will
                 * use a singular of a join table appended with _id
                 *
                 * ex.
                 * products -> product_id
                 */
                $prefix = Str::singular($sortTable);
                $keyName = $table.'.'.$prefix.'_id';
            }

            $model = $model
                ->leftJoin($sortTable, $keyName, '=', $sortTable.'.id')
                ->orderBy($sortColumn, $sortedBy)
                ->addSelect($table.'.*');
        } else {
            $model = $model->orderBy($orderBy, $sortedBy);
        }
        return $model;
    }

    /**
     * @param $search
     *
     * @return array
     */
    protected function parserSearchData($search)
    {
        $searchData = [];

        if (stripos($search, ':')) {
            $fields = explode(';', $search);

            foreach ($fields as $row) {
                try {
                    list($field, $value) = explode(':', $row);
                    $searchData[$field] = $value;
                } catch (\Exception $e) {
                    //Surround offset error
                }
            }
        }

        return $searchData;
    }

    /**
     * @param $search
     *
     * @return null
     */
    protected function parserSearchValue($search)
    {

        if (stripos($search, ';') || stripos($search, ':')) {
            $values = explode(';', $search);
            foreach ($values as $value) {
                $s = explode(':', $value);
                if (count($s) == 1) {
                    return $s[0];
                }
            }

            return null;
        }

        return $search;
    }


    protected function parserFieldsSearch(array $fields = [], array $searchFields = null)
    {
        if (!is_null($searchFields) && count($searchFields)) {
            $acceptedConditions = config('repository.criteria.acceptedConditions', [
                '=',
                'like'
            ]);
            $originalFields = $fields;
            $fields = [];

            foreach ($searchFields as $index => $field) {
                $field_parts = explode(':', $field);
                $temporaryIndex = array_search($field_parts[0], $originalFields);

                if (count($field_parts) == 2) {
                    if (in_array($field_parts[1], $acceptedConditions)) {
                        unset($originalFields[$temporaryIndex]);
                        $field = $field_parts[0];
                        $condition = $field_parts[1];
                        $originalFields[$field] = $condition;
                        $searchFields[$index] = $field;
                    }
                }
            }

            foreach ($originalFields as $field => $condition) {
                if (is_numeric($field)) {
                    $field = $condition;
                    $condition = "=";
                }
                if (in_array($field, $searchFields)) {
                    $fields[$field] = $condition;
                }
            }

            if (count($fields) == 0) {
                throw new \Exception(trans('repository::criteria.fields_not_accepted', ['field' => implode(',', $searchFields)]));
            }

        }

        return $fields;
    }
}
