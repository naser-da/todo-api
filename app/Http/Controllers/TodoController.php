<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{

    public function index()
    {
        return Auth::user()->todos()->latest()->get();
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $todo = Auth::user()->todos()->create($validated);
        
        return response()->json($todo, 201);
    }
    
    public function update(Request $request, Todo $todo)
    {
        $this->authorizeOwner($todo);
        
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_completed' => 'boolean',
        ]);
        
        $todo->update($validated);
        
        return response()->json($todo);
    }
    
    public function destroy(Todo $todo)
    {
        $this->authorizeOwner($todo);
        $todo->delete();
        
        return response()->json(['message' => 'Deleted']);
    }
    
    protected function authorizeOwner(Todo $todo)
    {
        if ($todo->user_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }
    }
    
}