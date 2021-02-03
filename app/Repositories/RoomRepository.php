<?php

namespace App\Repositories;

use App\Models\room;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class RoomRepository
{
    /**
     * @var room
     */
    protected $room;

    /**
     * Instantiate a new Repository instance.
     *
     * @param room $room
     */
    public function __construct(room $room)
    {
        $this->room = $room;
    }

    /**
     * Get all room
     *
     * @return array
     */
    public function getAll()
    {
        return $this->room->withCount('rooms')->get();
    }

    /**
     * List all room names
     *
     * @return array
     */
    public function list()
    {
        return $this->room->all()->pluck('name', 'id')->all();
    }

    /**
     * Find room by Id
     *
     * @param int|null $id
     *
     * @return room
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $room = $this->room->with('apartment')->Status()->FilterByUserId(Auth::user()->id)->find($id);

        if (!$room) {
            throw ValidationException::withMessages(['message' => trans('room.could_not_find')]);
        }

        return $room;
    }

    /**
     * Get room by apartment id
     *
     * @param string|null $apartment id
     *
     * @return room
     */
    public function filterByApartmentId($params)
    {
        $apartment_id = isset($params['apartment_id']) ? $params['apartment_id'] : null;
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'updated_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        if(isset($params['apartment_id'])){

           return $this->room->Status()->FilterByUserId(Auth::user()->id)->FilterByApartmentId($apartment_id)->orderBy($sort_by, $order)->paginate($page_length);

        }
        
        return $this->room->Status()->FilterByUserId(Auth::user()->id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * List all room by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->room->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all room by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->room->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all room names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->room->all()->pluck('name')->all();
    }

    /**
     * Find room and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return room
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $room = $this->findOrFail($id);

        if (in_array($room->name, config('system.default_category'))) {
            throw ValidationException::withMessages(['message' => trans('room.default_cannot_be_deleted')]);
        }

        return $room;
    }

    /**
     * Delete activity log.
     *
     * @param room $room
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(room $room)
    {
        try {
            return $room->delete();
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
        return $this->room->whereIn('id', $ids)->delete();
    }
}
