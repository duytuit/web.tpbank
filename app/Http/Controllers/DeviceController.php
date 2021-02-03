<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DeviceRepository;
use App\Models\device;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\DeviceRequest;

class DeviceController extends Controller
{
     /**
     * @var Request
     */
    protected $request;

    /**
     * @var DeviceRepository
     */
    protected $deviceRepo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'device';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param DeviceRepository $repo
     */
    public function __construct(Request $request, DeviceRepository $deviceRepo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->deviceRepo = $deviceRepo;
        $this->activity = $activity;
        //$this->middleware('permission:access-category');
    }

    /**
     * Get all device by apartment_id
     *
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $device = $this->deviceRepo->filterByRoomId($this->request->all())->toArray();
        $device['message'] = trans('device.device_list');
        return $this->success($device);
    }

    /**
     * Store device
     *
     *
     * @return JsonResponse
     */
    public function store(DeviceRequest $request)
    {

        if (device::where(['name'=>request('name'),'user_id'=> Auth::user()->id])->exists()) {
            return $this->error(['message' => trans('device.exists')]);
        }

        
        try {

            $device = device::create([
                'user_id' => Auth::user()->id,
                'room_id' => request('room_id') ?? null,
                'name' => request('name'),
            ]);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $device->id,
                'activity' => 'added'
            ]);

        } catch (QueryException $e) {
            throw $e;
        }
        return $this->success(['message' => trans('device.added')]);
    }

     /**
     * Update device
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(DeviceRequest $request, $id)
    {
        $device = $this->deviceRepo->findOrFail($id);
        if(!$device){
             return $this->error(null,trans('device.could_not_find'));
        }
        //$this->authorize('update', $apartment);

        
        try {
            
            $device->room_id = request('room_id') ?? null;
            $device->name = request('name');
            $device->status = request('status') ?? 1;
            $device->save();

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('device.updated')]);
    }

    /**
     * @param int $id
     *
     * @return \App\device
     * @throws ValidationException
     */
    public function show($id)
    {

        $device['data'] = $this->deviceRepo->findOrFail($id);
        $device['message'] = trans('device.detail');
        return $this->success($device);

    }

    /**
     * Delete device
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

            $device = $this->deviceRepo->deletable($id);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $device->id,
                'activity' => 'deleted'
            ]);

            $this->deviceRepo->delete($device);

        } catch (QueryException $e) {
            throw $e;
        }
       

        return $this->success(['message' => trans('device.deleted')]);
    }
}
