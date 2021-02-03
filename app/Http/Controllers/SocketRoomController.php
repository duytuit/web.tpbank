<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\Ws_socket_roomRepository;
use App\Repositories\ActivityLogRepository;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\ws_socket_roomRequest;
use App\Models\ws_socket_room;

class SocketRoomController extends Controller
{
      /**
     * @var Request
     */
    protected $request;

    /**
     * @var Ws_socket_roomRepository
     */
    protected $ws_socket_roomRepo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'ws_socket_room';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param Ws_socket_roomRepository $repo
     */
    public function __construct(Request $request, Ws_socket_roomRepository $ws_socket_roomRepo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->ws_socket_roomRepo = $ws_socket_roomRepo;
        $this->activity = $activity;
        //$this->middleware('permission:access-category');
    }

    /**
     * Get all ws_socket_room by apartment_id
     *
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $ws_socket_room = $this->ws_socket_roomRepo->filterCustom($this->request->all())->toArray();
        $ws_socket_room['message'] = trans('ws_socket_room.ws_socket_room_list');
        return $this->success($ws_socket_room);
    }

    /**
     * Store ws_socket_room
     *
     *
     * @return JsonResponse
     */
    public function store(ws_socket_roomRequest $request)
    {

        if (ws_socket_room::where(['Name'=>request('Name'),'Code'=>request('Code')])->exists()) {
            return $this->error(['message' => trans('ws_socket_room.exists')]);
        }

        
        try {

            $ws_socket_room = ws_socket_room::create([
                'Code' => request('Code'),
                'Hub' => request('Hub') ?? null,
                'Name' => request('Name'),
                'Users' => json_encode(request('Users')) ?? null,
                'Owner' => json_encode(request('Owner')) ?? null,
            ]);

        } catch (QueryException $e) {
            throw $e;
        }
        return $this->success(['message' => trans('ws_socket_room.added')]);
    }

     /**
     * Update ws_socket_room
     *
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(ws_socket_roomRequest $request, $id)
    {
        $ws_socket_room = $this->ws_socket_roomRepo->findOrFailByCode($id);
        if(!$ws_socket_room){
             return $this->error(null,trans('ws_socket_room.could_not_find'));
        }
        //$this->authorize('update', $apartment);

        
        try {
            
            $ws_socket_room->Code = request('Code');
            $ws_socket_room->Hub = request('Hub') ?? null;
            $ws_socket_room->Name = request('Name');
            $ws_socket_room->Users = json_encode(request('Users')) ?? null;
            $ws_socket_room->Owner = json_encode(request('Owner')) ?? null;
            $ws_socket_room->save();

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('ws_socket_room.updated')]);
    }

    /**
     * @param int $id
     *
     * @return \App\ws_socket_room
     * @throws ValidationException
     */
    public function show($id)
    {

        $ws_socket_room['data'] = $this->ws_socket_roomRepo->findOrFail($id);
        $ws_socket_room['message'] = trans('ws_socket_room.detail');
        return $this->success($ws_socket_room);

    }

    /**
     * Delete ws_socket_room
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

            $ws_socket_room = $this->ws_socket_roomRepo->deletable($id);

            $this->ws_socket_roomRepo->delete($ws_socket_room);

        } catch (QueryException $e) {
            throw $e;
        }
       

        return $this->success(['message' => trans('ws_socket_room.deleted')]);
    }
}
