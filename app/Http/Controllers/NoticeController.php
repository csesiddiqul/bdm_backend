<?php

namespace App\Http\Controllers;

use App\Classes\ImageUpload;
use App\Http\API\BaseController;
use App\Models\Notice;
use App\Http\Requests\StoreNoticeRequest;
use App\Http\Requests\UpdateNoticeRequest;
use App\Http\Resources\web\NoticeResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NoticeController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $notice = Notice::when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })->orderBy('sorting_index', 'ASC')->paginate($request->per_page ?? 15);

            return NoticeResource::collection($notice);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
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
    public function store(StoreNoticeRequest $request, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $notice = Notice::create([
                'pdf' => null,
                'sorting_index' => $request->sorting_index,
                'date' => $request->date,
                'title' => $request->title,
                'status' => $request->status,
            ]);
            if ($request->pdf) {
                $notice->pdf = $imageUpload->fileUpload(
                    file: $request->pdf,
                    data: $notice,
                    folder: 'notice-pdf',
                    fileName: 'pdf_' . $notice->id
                );
                $notice->save();
            }
            DB::commit();
            return $this->sendResponse(
                message: 'notice created successfully.',
                data: new NoticeResource($notice),
                status: 201
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(
                message: 'notice creation failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Notice $notice)
    {
        try {
            $notice = Notice::findOrFail($notice->id);
            return (new NoticeResource($notice))->additional([
                "success" => true,
                "message" => __("notice  show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('notice  Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('notice Show failed.', $exception->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notice $notice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoticeRequest $request, Notice $notice, ImageUpload $imageUpload)
    {
        DB::beginTransaction();
        try {
            $notice->sorting_index = $request->sorting_index;
            $notice->title = $request->title;
            $notice->date = $request->date;
            $notice->status = $request->status;
            if ($request->pdf) {
                if ($notice->pdf) {
                    $imageUpload->deleteFile($notice->pdf, 'notice-pdf');
                }

                $notice->pdf = $imageUpload->fileUpload(
                    file: $request->pdf,
                    data: $notice,
                    folder: 'notice-pdf',
                    fileName: 'pdf_' . $notice->id
                );
            }
            $notice->save();
            DB::commit();
            return $this->sendResponse(
                message: 'notice  updated successfully.',
                data: new NoticeResource($notice),
                status: 200
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->sendError(
                message: 'notice  update failed.',
                errors: $e->getMessage(),
                status: 500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notice $notice, ImageUpload $imageUpload)
    {
        try {
            DB::beginTransaction();
            $notice = Notice::findOrFail(id: $notice->id);
            if ($notice->image) {
                $imageUpload->deleteFile($notice->image, folder: 'notice-pdf');
            }
            $notice->delete();
            DB::commit();
            return (new NoticeResource($notice))->additional([
                "success" => true,
                "message" => __("notice Delete successfully")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('notice Delete failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('notice Delete failed.', $exception->getMessage());
        }
    }
}
