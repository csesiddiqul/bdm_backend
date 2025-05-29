<?php

namespace App\Http\Controllers;

use App\Http\API\BaseController;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Resources\web\ServiceResource;
use App\Models\Service;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $services = Service::when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })->orderBy('sorting_index', 'asc')->paginate($request->per_page ?? 15);

            return ServiceResource::collection($services);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('BlogNews Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('BlogNews Show failed.', $exception->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreServiceRequest $request)
    {
        DB::beginTransaction();
        try {
            $service = Service::create([
                'icon' =>  $request->icon,
                'sorting_index' => $request->sorting_index,
                'title' => $request->title,
                'description' =>  $request->description,
                'status' => $request->status,
            ]);

            DB::commit();
            return $this->sendResponse(
                message: 'Data created successfully.',
                data: new ServiceResource($service),
                status: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'Data creation failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $service = Service::findOrFail($id);
            return (new ServiceResource($service))->additional([
                "success" => true,
                "message" => __("Data show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreServiceRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $service = Service::where('id', '=', $id)->firstOrFail();
            $service->update([
                'icon' => $request->icon,
                'sorting_index' => $request->sorting_index,
                'title' => $request->title,
                'description' => $request->description,
                'status' => $request->status,
            ]);

            DB::commit();
            return $this->sendResponse(
                message: 'Data updated successfully.',
                data: new ServiceResource($service),
                status: 200
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'Data update failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {

        try {
            DB::beginTransaction();
            $service = Service::findOrFail(id: $id);

            $service->delete();
            DB::commit();
            return (new ServiceResource($service))->additional([
                "success" => true,
                "message" => __("Data Delete successfully")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Delete failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Data Delete failed.', $exception->getMessage());
        }
    }
}
