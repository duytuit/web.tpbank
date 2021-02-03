<?php

namespace App\Repositories;

use App\Models\type_device;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class TypeDeviceRepository
{
    /**
     * @var type_device
     */
    protected $type_device;

    /**
     * Instantiate a new Repository instance.
     *
     * @param type_device $type_device
     */
    public function __construct(type_device $type_device)
    {
        $this->type_device = $type_device;
    }

    /**
     * Get all type_device
     *
     * @return array
     */
    public function getAll()
    {
        return $this->type_device->withCount('type_devices')->get();
    }

    /**
     * List all type_device names
     *
     * @return array
     */
    public function list()
    {
        return $this->type_device->all()->pluck('name', 'id')->all();
    }

    /**
     * Find type_device by Id
     *
     * @param int|null $id
     *
     * @return type_device
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $type_device = $this->type_device->find($id);

        if (!$type_device) {
            throw ValidationException::withMessages(['message' => trans('type_device.could_not_find')]);
        }

        return $type_device;
    }

    /**
     * Get type_device by user_id
     *
     * @param string|null $user_id
     *
     * @return type_device
     */
    public function filterByUserId($params)
    {
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'updated_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->type_device->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * List all type_device by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->type_device->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all type_device by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->type_device->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all type_device names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->type_device->all()->pluck('name')->all();
    }

    /**
     * Paginate all activity logs using given params.
     *
     * @param array $params
     *
     * @return LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->type_device->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Find type_device and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return type_device
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $type_device = $this->findOrFail($id);

        if (in_array($type_device->name, config('system.default_category'))) {
            throw ValidationException::withMessages(['message' => trans('type_device.default_cannot_be_deleted')]);
        }

        return $type_device;
    }

    /**
     * Delete activity log.
     *
     * @param type_device $type_device
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(type_device $type_device)
    {
        try {
            return $type_device->delete();
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
        return $this->type_device->whereIn('id', $ids)->delete();
    }
}
