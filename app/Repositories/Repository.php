<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

abstract class Repository
{
    protected $model;
    protected array $searchable = [];

    public function get(array $attributes = [], array $columns = ['*'])
    {
        $defaultData = [
            'order_by' => 'id',
            'order'    => 'DESC',
            'where'    => [],
            'with'     => [],
            'search'   => '',
            'filters'  => []
        ];

        $data = array_merge($defaultData, $attributes);

        $query = $this->model->query();

        $query = $this->applyWhere($query, $data['where'] ?: []);
        $query = $this->applyWith($query, $data['with'] ?: []);
        $query = $this->applySearch($query, $data['search'] ?: '');
        $query = $this->applyFilters($query, $data['filters'] ?: []);
        $query = $this->applyOrder($query, $data['order_by'], $data['order']);

        return $query->get($columns);
    }

    public function paginate(array $attributes = [], array $columns = ['*'])
    {
        $defaultData = [
            'order_by' => 'id',
            'order'    => 'DESC',
            'where'    => [],
            'with'     => [],
            'search'   => '',
            'filters'  => [],
            'per_page' => 15,
            'page'     => 1
        ];

        $data = array_merge($defaultData, $attributes);

        $query = $this->model->query()->select($columns);

        $query = $this->applySearch($query, $data['search'] ?: '');
        $query = $this->applyWhere($query, $data['where'] ?: []);
        $query = $this->applyFilters($query, $data['filters'] ?: []);
        $query = $this->applyWith($query, $data['with'] ?: []);
        $query = $this->applyOrder($query, $data['order_by'], $data['order']);

        return $query->paginate($data['per_page'], ['*'], 'page', $data['page']);
    }


    private function applyWhere($query, array $conditions)
    {
        foreach ($conditions as $condition) {
            if (count($condition) === 2) {
                $query->where($condition[0], $condition[1]);
            } elseif (count($condition) === 3) {
                $query->where($condition[0], $condition[1], $condition[2]);
            }
        }
        return $query;
    }

    private function applyWith($query, array $relations)
    {
        if (!empty($relations)) {
            $query->with($relations);
        }
        return $query;
    }

    private function applySearch($query, $searchKey)
    {
        $query->where(function ($query) use ($searchKey) {
            foreach ($this->searchable as $searchable) {
                $query->orWhere($searchable, 'LIKE', '%' . $searchKey . '%');
            }
        });

        return $query;
    }

    private function applyFilters($query, array $filters)
    {
        foreach ($filters as $field => $value) {
            $query->where($field, $value);
        }
        return $query;
    }

    private function applyOrder($query, $orderBy, $order)
    {
        return $query->orderBy($orderBy, $order);
    }
}
