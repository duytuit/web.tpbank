<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TypeDeviceRepository;
use App\Models\type_device;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TypeDeviceRequest;

class TypeDeviceController extends Controller
{
      /**
     * @var Request
     */
    protected $request;

    /**
     * @var TypeDeviceRepository
     */
    protected $type_deviceRepo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'type_device';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param TypeDeviceRepository $repo
     */
    public function __construct(Request $request, TypeDeviceRepository $type_deviceRepo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->type_deviceRepo = $type_deviceRepo;
        $this->activity = $activity;
        //$this->middleware('permission:access-category');
    }

    /**
     * Get all type_device by apartment_id
     *
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $type_device = $this->type_deviceRepo->paginate($this->request->all())->toArray();
        $type_device['message'] = trans('type_device.type_device_list');
        return $this->success($type_device);
    }

    /**
     * Store type_device
     *
     *
     * @return JsonResponse
     */
    public function store(TypeDeviceRequest $request)
    {

        if (type_device::where('name', request('name'))->exists()) {
            return $this->error(['message' => trans('type_device.exists')]);
        }

        try {

             $type_device = type_device::create([
            'name' => request('name'),
            'type' => request('type'),
            'feature' => request('feature')
            ]);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $type_device->id,
                'activity' => 'added'
            ]);

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('type_device.added')]);
    }

     /**
     * Update type_device
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(TypeDeviceRequest $request, $id)
    {
        $type_device = $this->type_deviceRepo->findOrFail($id);
        if(!$type_device){
             return $this->error(null,trans('type_device.could_not_find'));
        }
        //$this->authorize('update', $apartment);

        
        try {

            $type_device->name = request('name');
            $type_device->type = request('type');
            $type_device->feature = request('feature');
            $type_device->status = request('status') ?? 1;
            $type_device->save();

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('type_device.updated')]);
    }

    /**
     * @param int $id
     *
     * @return \App\type_device
     * @throws ValidationException
     */
    public function show($id)
    {

        $type_device['data'] = $this->type_deviceRepo->findOrFail($id);
        $type_device['message'] = trans('type_device.detail');
        return $this->success($type_device);

    }

    /**
     * Delete type_device
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws ValidationException
     * @throws \Exception
     */
    public function destroy($id)
    {
        try {

            $type_device = $this->type_deviceRepo->deletable($id);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $type_device->id,
                'activity' => 'deleted'
            ]);

            $this->type_deviceRepo->delete($type_device);

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('type_device.deleted')]);
    }
}
