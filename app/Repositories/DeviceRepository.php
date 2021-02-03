<?php

namespace App\Repositories;

use App\Models\device;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class DeviceRepository
{
    /**
     * @var device
     */
    protected $device;

    /**
     * Instantiate a new Repository instance.
     *
     * @param device $device
     */
    public function __construct(device $device)
    {
        $this->device = $device;
    }

    /**
     * Get all device
     *
     * @return array
     */
    public function getAll()
    {
        return $this->device->withCount('devices')->get();
    }

    /**
     * List all device names
     *
     * @return array
     */
    public function list()
    {
        return $this->device->all()->pluck('name', 'id')->all();
    }

    /**
     * Find device by Id
     *
     * @param int|null $id
     *
     * @return device
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $device = $this->device->with('room','user')->Status()->FilterByUserId(Auth::user()->id)->find($id);

        if (!$device) {
            throw ValidationException::withMessages(['message' => trans('device.could_not_find')]);
        }

        return $device;
    }

    /**
     * Get device by room id
     *
     * @param string|null $room id
     *
     * @return device
     */
    public function filterByRoomId($params)
    {
        $room_id = isset($params['room_id']) ? $params['room_id'] : null;
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'updated_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        if(isset($params['room_id'])){

            return $this->device->Status()->FilterByUserId(Auth::user()->id)->FilterByRoomId($room_id)->orderBy($sort_by, $order)->paginate($page_length);

        }
        return $this->device->Status()->FilterByUserId(Auth::user()->id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * List all device by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->device->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all device by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->device->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all device names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->device->all()->pluck('name')->all();
    }

    /**
     * Find device and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return device
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $device = $this->findOrFail($id);

        if (in_array($device->name, config('system.default_category'))) {
            throw ValidationException::withMessages(['message' => trans('device.default_cannot_be_deleted')]);
        }

        return $device;
    }

    /**
     * Delete activity log.
     *
     * @param device $device
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(device $device)
    {
        try {
            return $device->delete();
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
        return $this->device->whereIn('id', $ids)->delete();
    }
}
