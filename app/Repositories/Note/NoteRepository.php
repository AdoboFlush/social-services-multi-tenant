<?php

namespace App\Repositories\Note;

use App\Note;
use Illuminate\Database\Eloquent\Collection; 

class NoteRepository implements NoteInterface
{
    private $model;

    public function __construct(Note $model)
    {
        $this->model = $model;
    }

    public function create($request): Note
    {
        return $this->model->create($request);
    }

    public function update($id, $request): bool
    {
        return $this->model->find($id)->update($request);
    }

    public function delete($id): bool
    {
        return $this->model->find($id)->delete();
    }

    public function get($id): Note
    {
        return $this->model->find($id);
    }

    public function getAll(): Collection
    {
        return $this->model->get();
    }

    public function getNote(string $note_slug): ?string
    {
        return $this->model::getNote($note_slug);
    }
}
