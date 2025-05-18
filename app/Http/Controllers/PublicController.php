<?php

namespace App\Http\Controllers;

use App\Http\API\BaseController;
use App\Http\Resources\SliderResource;
use App\Models\Slider;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class PublicController extends BaseController
{
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
}
