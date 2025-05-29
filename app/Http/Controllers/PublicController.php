<?php

namespace App\Http\Controllers;

use App\Http\API\BaseController;
use App\Http\Resources\Auth\UserProfileResource;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\web\BdmHospitalResource;
use App\Http\Resources\web\BlogNewsResource;
use App\Http\Resources\web\MissionVisionResource;
use App\Http\Resources\web\NoticeResource;
use App\Http\Resources\web\ServiceResource;
use App\Http\Resources\web\WishersResource;
use App\Models\BdmHospital;
use App\Models\BlogNews;
use App\Models\Gallery;
use App\Models\MissionVision;
use App\Models\Notice;
use App\Models\Service;
use App\Models\Slider;
use App\Models\User;
use App\Models\Wishers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PublicController extends BaseController
{

    public function missionVision()
    {
        try {
            $missionVision = MissionVision::findOrFail(1);
            return (new MissionVisionResource($missionVision))->additional([
                "success" => true,
                "message" => __("Data show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Not Found.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Not Found.', $exception->getMessage());
        }
    }

    public function bmsAbout()
    {
        try {
            $bdmHospital = BdmHospital::findOrFail(1);
            return (new BdmHospitalResource($bdmHospital))->additional([
                "success" => true,
                "message" => __("Data show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Not Found.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Not Found.', $exception->getMessage());
        }
    }

    public function slider(Request $request): mixed
    {
        try {
            $sliders = Slider::where('status', 1)->orderBy('created_at', 'DESC')->get();
            return SliderResource::collection($sliders);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Slider data failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Slider data failed.', $exception->getMessage());
        }
    }


    public function videoGallery(Request $request)
    {
        try {
            $slider = Gallery::where('status', '=', '1')
                ->where('type', '=', 'video')
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->id, function ($q, $id) {
                    $q->where('id', $id);
                })->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })->orderBy('sorting_index', 'ASC')->paginate($request->per_page ?? 15);

            return GalleryResource::collection($slider);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Slider Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Slider Show failed.', $exception->getMessage());
        }
    }
    public function noticeData(Request $request)
    {
        try {
            $notice = Notice::where('status', 1)->when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', 1);
                })->orderBy('sorting_index', 'DESC')->paginate($request->per_page ?? 15);
            return NoticeResource::collection($notice);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        }
    }
    public function wishers(Request $request)
    {
        try {
            $wishers = Wishers::where('status', 1)->when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', 1);
                })->orderBy('sorting_index', 'DESC')->paginate($request->per_page ?? 105);
            return WishersResource::collection($wishers);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data Show failed.', $exception->getMessage());
        }
    }


    public function blogNews(Request $request)
    {
        try {
            $blogNews = BlogNews::where('status', 1)->when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })->orderBy('sorting_index', 'asc')->paginate($request->per_page ?? 15);

            return BlogNewsResource::collection($blogNews);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        }
    }

    public function blogNewsDetails($id)
    {
        try {
            $blogNews = BlogNews::findOrFail($id);
            return (new BlogNewsResource($blogNews))->additional([
                "success" => true,
                "message" => __("data show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        }
    }
    public function doctorProfile(Request $request)
    {
        try {
            $query = User::with('doctorProfile')->whereHas('doctorProfile', function ($q) {
                $q->where('status', 1);
            })
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('email', 'like', '%' . $request->search . '%')
                        ->orWhereHas('doctorProfile', function ($q) use ($request) {
                            $q->where('designation', 'like', '%' . $request->search . '%')
                                ->orWhere('specialization', 'like', '%' . $request->search . '%')
                                ->orWhere('department', 'like', '%' . $request->search . '%');
                        });
                });

            $query->when($request->id, function ($q) use ($request) {
                $q->where('id', $request->id);
            });

            $query->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            });

            $doctors = $query->orderBy('created_at', 'asc')
                ->paginate($request->per_page ?? 15);

            return UserProfileResource::collection($doctors);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        }
    }

    public function doctorProfileDetails($id)
    {
        try {
            $user = User::with('doctorProfile')
                ->whereHas('doctorProfile', function ($query) {
                    $query->where('status', 1); // Only active doctor profiles
                })
                ->find($id);
            return (new UserProfileResource($user))->additional([
                "success" => true,
                "message" => __("Data show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('Data show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('Data show failed.', $exception->getMessage());
        }
    }





    public function services(Request $request)
    {
        try {
            $blogNews = Service::where('status', 1)->when($request->id, function ($q, $id) {
                $q->where('id', $id);
            })
                ->when($request->search, function ($q, $search) {
                    $q->whereRaw('LOWER(title) LIKE ?', ['%' . strtolower($search) . '%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($search) . '%']);
                })
                ->when($request->status, function ($q, $status) {
                    $q->where('status', $status);
                })->orderBy('sorting_index', 'asc')->paginate($request->per_page ?? 15);

            return ServiceResource::collection($blogNews);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        }
    }

    public function servicesDetails($id)
    {
        try {
            $blogNews = Service::findOrFail($id);
            return (new ServiceResource($blogNews))->additional([
                "success" => true,
                "message" => __("data show successfully.")
            ]);
        } catch (ModelNotFoundException $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        } catch (\Exception $exception) {
            return $this->sendError('data Show failed.', $exception->getMessage());
        }
    }
}
