<?php

namespace App\Http\Controllers;

use App\Repositories\TodoRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TodosController extends Controller
{
    private $todoRepo;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(TodoRepository $todoRepo)
    {
        // Construct
        $this->todoRepo = $todoRepo;
    }

    public function index()
    {
        $response = [
            'message' => 'Hello from API Index',
        ];
        return new JsonResponse($response, 200);
    }

    public function indexTodos()
    {
        return $this->todoRepo->all();
    }

    public function viewTodo($id)
    {
        $result = $this->todoRepo->get($id);
        return $result;
    }

    public function createTodo(Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $data = [
            'title' => $request->input('title'),
            'due_date' => $request->input('due_date', null),
            'color' => $request->input('color', null),
            'todo_groups_id' => $request->input('todo_groups_id'),
            'marked' => $request->input('marked', 0)
        ];

        $created = $this->todoRepo->create($data);
        if (!$created) {
            return response(['message' => 'Couldn\'t create Todo'], 400);
        }
        return $created;
    }

    public function updateTodo($id, Request $request)
    {
        $this->validate($request, [
            'title' => 'required'
        ]);

        $data = [
            'title' => $request->input('title'),
            'due_date' => $request->input('due_date', null),
            'color' => $request->input('color', null),
            'marked' => $request->input('marked', 0)
        ];

        $updated = $this->todoRepo->update($data, $id);
        if (!$updated) {
            return response(['message' => 'Couldn\'t update the Todo'], 400);
        }
        return $updated;
    }

    public function deleteTodo($id)
    {
        $deleted = $this->todoRepo->delete($id);
        if (!$deleted) {
            return response(['message' => 'Couldn\'t delete the Todo'], 400);
        }
        return $deleted;
    }

    public function moveTodo($id, Request $request)
    {
        $moved = $this->todoRepo->move($id, $request->input('prior_sibling_id', ''));
        if (!$moved) {
            return response(['message' => 'Couldn\'t move the Todo'], 400);
        }
        return $moved;
    }
}
