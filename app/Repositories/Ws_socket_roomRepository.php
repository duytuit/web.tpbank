<?php

namespace App\Repositories;

use App\Models\ws_socket_room;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class Ws_socket_roomRepository
{
    /**
     * @var ws_socket_room
     */
    protected $ws_socket_room;

    /**
     * Instantiate a new Repository instance.
     *
     * @param ws_socket_room $ws_socket_room
     */
    public function __construct(ws_socket_room $ws_socket_room)
    {
        $this->ws_socket_room = $ws_socket_room;
    }

    /**
     * Get all ws_socket_room
     *
     * @return array
     */
    public function getAll()
    {
        return $this->ws_socket_room->withCount('ws_socket_rooms')->get();
    }

    /**
     * List all ws_socket_room names
     *
     * @return array
     */
    public function list()
    {
        return $this->ws_socket_room->all()->pluck('Name', 'id')->all();
    }

    /**
     * Find ws_socket_room by Id
     *
     * @param int|null $id
     *
     * @return ws_socket_room
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $ws_socket_room = $this->ws_socket_room->find($id);

        if (!$ws_socket_room) {
            throw ValidationException::withMessages(['message' => trans('ws_socket_room.could_not_find')]);
        }

        return $ws_socket_room;
    }
     /**
     * Find ws_socket_room by Id
     *
     * @param int|null $id
     *
     * @return ws_socket_room
     * @throws ValidationException
     */
    public function findOrFailByCode($code = null)
    {
        $ws_socket_room = $this->ws_socket_room->where('Code',$code)->first();

        if (!$ws_socket_room) {
            throw ValidationException::withMessages(['message' => trans('ws_socket_room.could_not_find')]);
        }

        return $ws_socket_room;
    }

    /**
     * Get ws_socket_room by room id
     *
     * @param string|null $room id
     *
     * @return ws_socket_room
     */
    public function filterCustom($params)
    {

        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'updated_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        if(isset($params['Code'])){

            return $this->ws_socket_room->where('Code',$params['Code'])->orderBy($sort_by, $order)->paginate($page_length);

        }
        if(isset($params['Hub'])){

            return $this->ws_socket_room->where('Hub',$params['Hub'])->orderBy($sort_by, $order)->paginate($page_length);

        }
        return $this->ws_socket_room->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * List all ws_socket_room by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->ws_socket_room->whereNotIn('Name', $names)->get()->pluck('Name', 'id')->all();
    }

    /**
     * List all ws_socket_room by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->ws_socket_room->whereIn('id', $ids)->get()->pluck('Name')->all();
    }

    /**
     * List all ws_socket_room names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->ws_socket_room->all()->pluck('Name')->all();
    }

    /**
     * Find ws_socket_room and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return ws_socket_room
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $ws_socket_room = $this->findOrFail($id);

        if (in_array($ws_socket_room->name, config('system.default_category'))) {
            throw ValidationException::withMessages(['message' => trans('ws_socket_room.default_cannot_be_deleted')]);
        }

        return $ws_socket_room;
    }

    /**
     * Delete activity log.
     *
     * @param ws_socket_room $ws_socket_room
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(ws_socket_room $ws_socket_room)
    {
        try {
            return $ws_socket_room->delete();
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1451) {
                throw ValidationException::withMessages(['message' => 'error']);
            } else {
                throw $e;
            }
        }
    }

    /**
     * Delete multiple activity logs.
     *
     * @param array $ids
     *
     * @return bool|null
     * @throws \Exception
     */
    public function deleteMultiple($ids = array())
    {
        return $this->ws_socket_room->whereIn('id', $ids)->delete();
    }
}
