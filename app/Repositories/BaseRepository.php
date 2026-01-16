<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

abstract class BaseRepository implements RepositoryInterface
{
    protected Model $model;
    protected int $perPage = 15;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all(): LengthAwarePaginator
    {
        return $this->model->latest()->paginate($this->perPage);
    }

    public function get(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->latest()->get();
    }

    public function find(string $id): ?Model
    {
        return $this->model->find($id);
    }

    public function findOrFail(string $id): Model
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(string $id, array $data): Model
    {
        $model = $this->findOrFail($id);
        $model->update($data);
        return $model->fresh();
    }

    public function delete(string $id): bool
    {
        return $this->findOrFail($id)->delete();
    }

    public function setPerPage(int $perPage): self
    {
        $this->perPage = $perPage;
        return $this;
    }
}
