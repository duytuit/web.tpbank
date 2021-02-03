<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\RoomRepository;
use App\Models\room;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RoomRequest;

class RoomController extends Controller
{
     /**
     * @var Request
     */
    protected $request;

    /**
     * @var RoomRepository
     */
    protected $roomRepo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'room';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param RoomRepository $repo
     */
    public function __construct(Request $request, RoomRepository $roomRepo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->roomRepo = $roomRepo;
        $this->activity = $activity;
        //$this->middleware('permission:access-category');
    }

    /**
     * Get all Room by apartment_id
     *
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $room = $this->roomRepo->filterByApartmentId($this->request->all())->toArray();
        $room['message'] = trans('room.room_list');
        return $this->success($room);
    }

    /**
     * Store Room
     *
     *
     * @return JsonResponse
     */
    public function store(RoomRequest $request)
    {

        if (room::where(['name'=>request('name'),'user_id'=> Auth::user()->id])->exists()) {
            return $this->error(['message' => trans('room.exists')]);
        }

        
         try {

            $room = room::create([
                'apartment_id' => request('apartment_id'),
                'user_id' => Auth::user()->id,
                'name' => request('name'),
                'image' => request('image') ?? null
            ]);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $room->id,
                'activity' => 'added'
            ]);

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('room.added')]);
    }

     /**
     * Update Room
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(RoomRequest $request, $id)
    {
        $room = $this->roomRepo->findOrFail($id);
        if(!$room){
             return $this->error(null,trans('room.could_not_find'));
        }
        //$this->authorize('update', $apartment);

        
        try {

            $room->name = request('name');
            $room->image = request('image');
            $room->status = request('status') ?? 1;
            $room->save();

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('room.updated')]);
    }

    /**
     * @param int $id
     *
     * @return \App\room
     * @throws ValidationException
     */
    public function show($id)
    {

        $room['data'] = $this->roomRepo->findOrFail($id);
        $room['message'] = trans('room.detail');
        return $this->success($room);

    }

    /**
     * Delete Room
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

            $room = $this->roomRepo->deletable($id);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $room->id,
                'activity' => 'deleted'
            ]);

            $this->roomRepo->delete($room);

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('room.deleted')]);
    }
}
