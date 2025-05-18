<?php

namespace App\Http\Controllers;

use App\Http\API\BaseController;
use App\Http\Resources\Auth\RoleResource;
use App\Http\Resources\Auth\RolesResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission as ModelsPermission;
use App\Http\Resources\Auth\PermissionResource;

class CustomizeRoleController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $roles = Role::with('permissions') // Eager load permissions
                ->when($request->id, function ($query, $id) {
                    $query->where('id', $id);
                })
                // ->whereNotIn('id', [1, 7, 8 , 10, 11, 12]) // Excludes specific IDs
                ->orderBy('created_at', 'ASC')
                ->paginate($request->per_page ?? 15);

            return RolesResource::collection($roles);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError(
                'Role retrieval failed.',
                $exception->getMessage(),
                404
            );
        } catch (\Exception $exception) {
            // Return a general error response for other exceptions
            return $this->sendError('Role retrieval failed.', $exception->getMessage());
        }
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Permission created successfully.',
            'data' => $permission,
        ], 201);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        try {
            $role = Role::create([
                'name' => $validatedData['name'],
                'guard_name' => 'web',
            ]);

            if (!empty($validatedData['permissions'])) {
                $role->permissions()->sync($validatedData['permissions']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'data' => $role->load('permissions'),
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('role  created failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('role created failed.', $exception->getMessage());
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $role = Role::findOrFail($id);

            return $this->sendResponse(
                __('Role show successfully.'),
                new RoleResource($role)
            );
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('role  Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('role Show failed.', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */


    public function permissionsAll(Request $request)
    {
        try {
            $permissions = ModelsPermission::where('guard_name', 'web')
                ->orderBy('created_at', 'ASC')
                ->paginate($request->per_page ?? 15);

            // Return the resource collection for permissions
            return PermissionResource::collection($permissions);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data retrieval failed. No permissions found.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data retrieval failed.', $exception->getMessage());
        }
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'permissions' => 'nullable|array',
            'permissions.*' => 'integer',
        ]);
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found.',
            ], 404);
        }
        if ($request->all == 1) {
            $allPermissions = Permission::where('guard_name', 'web')->pluck('name')->toArray();
            $role->syncPermissions($allPermissions);
        } else {
            if (isset($validatedData['permissions'])) {
                $existingPermissions = Permission::whereIn('id', $validatedData['permissions'])
                    ->where('guard_name', 'web')
                    ->pluck('name')
                    ->toArray();
                if (count($existingPermissions) !== count($validatedData['permissions'])) {
                    return response()->json([
                        'success' => false,
                        'message' => 'One or more permissions are invalid or do not exist.',
                    ], 422);
                }
                $role->syncPermissions($existingPermissions);
            }
        }
        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully.',
            'data' => $role->load('permissions'), // Load permissions for response
        ]);
    }

    public function destroy(string $id)
    {
        //
    }
}
