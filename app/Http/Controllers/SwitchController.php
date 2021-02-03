<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SwitchRepository;
use App\Models\switche;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\SwitchRequest;

class SwitchController extends Controller
{
      /**
     * @var Request
     */
    protected $request;

    /**
     * @var SwitchRepository
     */
    protected $switchRepo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'switch';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param SwitchRepository $repo
     */
    public function __construct(Request $request, SwitchRepository $switchRepo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->switchRepo = $switchRepo;
        $this->activity = $activity;
        //$this->middleware('permission:access-category');
    }

    /**
     * Get all switch by device_id
     *
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $switch = $this->switchRepo->filterCustom($this->request->all())->toArray();
        $switch['message'] = trans('switch.switch_list');
        // list search with option parameter
        $switch['option'] = [
            '/api/switch?apartment_id=?',
            '/api/switch?room_id=?',
            '/api/switch?device_id=?'
        ];
        return $this->success($switch);
    }

    /**
     * Store switch
     *
     *
     * @return JsonResponse
     */
    public function store(SwitchRequest $request)
    {

        if (switche::where(['name'=>request('name'),'user_id'=> Auth::user()->id])->exists()) {
            return $this->error(['message' => trans('switch.exists')]);
        }

       
        try {

             $switch = switche::create([
                'device_id' => request('device_id') ?? null,
                'user_id' => Auth::user()->id,
                'name' => request('name'),
                'image' => request('image'),
                'notify' => request('notify') ?? 0,
                'interval' => request('interval') ?? null,
                'action' => request('action') ?? 0,
                'type_id' => request('type_id'),
            ]);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $switch->id,
                'activity' => 'added'
            ]);

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('switch.added')]);
    }

     /**
     * Update switch
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(SwitchRequest $request, $id)
    {
        $switch = $this->switchRepo->findOrFail($id);
        if(!$switch){
             return $this->error(null,trans('switch.could_not_find'));
        }
        //$this->authorize('update', $apartment);

        
        try {

            $switch->device_id = request('device_id') ?? null;
            $switch->name = request('name');
            $switch->image = request('image');
            $switch->notify = request('notify') ?? 0;
            $switch->interval = request('interval') ?? null;
            $switch->action = request('action') ?? 0;
            $switch->status = request('status') ?? 1;
            $switch->save();

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('switch.updated')]);
    }

    /**
     * @param int $id
     *
     * @return \App\switch
     * @throws ValidationException
     */
    public function show($id)
    {

        $switch['data'] = $this->switchRepo->findOrFail($id);
        $switch['message'] = trans('switch.detail');
        return $this->success($switch);

    }

    /**
     * Delete switch
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

            $switch = $this->switchRepo->deletable($id);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $switch->id,
                'activity' => 'deleted'
            ]);

            $this->switchRepo->delete($switch);

        } catch (QueryException $e) {
            throw $e;
        }
       
        return $this->success(['message' => trans('switch.deleted')]);
    }
}
