<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Traits\ResponseFormatter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\Finder\Exception\AccessDeniedException;

class RoleController extends Controller
{
    public function store(Request $request)
    {
        $this->authorize('create', Role::class);

        $request->validate([
            'role' => "required|string|unique:roles,role"
        ]);

        $role = Role::create(['role' => $request->role]);
        if (!$role) {
            return $this->error(400, "Bad request", null);
        }


        $this->success(200, "Role created", $role);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('update', Role::class);

        $role = Role::findOrFail($id);

        $request->validate([
            'role' => "string|unique:roles,role"
        ]);

        $role->fill(["role" => $request->role])->saveOrFail();

        $this->success(200, "Role created", $role->fresh());
    }


    public function destroy(Request $request, $id)
    {
        $this->authorize('delete', Role::class);

        $role = Role::findOrFail($id);

        $request->validate([
            'role' => "string|unique:roles,role"
        ]);

        $role->fill(["role" => $request->role])->saveOrFail();

        $this->success(200, "Role created", $role->fresh());
    }
}
