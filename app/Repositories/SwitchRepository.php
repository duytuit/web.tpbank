<?php

namespace App\Repositories;

use App\Models\switche;
use App\Models\device;
use App\Models\room;
use App\Models\apartment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class SwitchRepository
{
    /**
     * @var switche
     */
    protected $switche;

    /**
     * Instantiate a new Repository instance.
     *
     * @param switche $switche
     */
    public function __construct(switche $switche)
    {
        $this->switche = $switche;
    }

    /**
     * Get all switche
     *
     * @return array
     */
    public function getAll()
    {
        return $this->switche->withCount('switches')->get();
    }

    /**
     * List all switche names
     *
     * @return array
     */
    public function list()
    {
        return $this->switche->all()->pluck('name', 'id')->all();
    }

    /**
     * Find switche by Id
     *
     * @param int|null $id
     *
     * @return switche
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $switche = $this->switche->with('device','user')->Status()->FilterByUserId(Auth::user()->id)->find($id);

        if (!$switche) {
            throw ValidationException::withMessages(['message' => trans('device.could_not_find')]);
        }

        return $switche;
    }

    /**
     * Get switche by device id
     *
     * @param string|null $device id
     *
     * @return switche
     */
    public function filterByDeviceId($params)
    {
        $device_id = isset($params['device_id']) ? $params['device_id'] : null;
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'updated_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->switche->FilterByDeviceId($device_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Get switche by CustomFilter
     *
     * @param string|null $apartment id
     *
     * @return switche
     */
    public function filterCustom($params)
    {

        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'updated_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        if(isset($params['apartment_id'])){

            $list_room = room::where('apartment_id', $params['apartment_id'])->pluck('id')->toArray();

            $list_device = device::whereIn('room_id', $list_room)->pluck('id')->toArray();
    
            return $this->switche->with('type_device')->Status()->FilterByUserId(Auth::user()->id)->whereIn('device_id', $list_device)->orderBy($sort_by, $order)->paginate($page_length);
        }
        if(isset($params['room_id'])){

            $list_device = device::where('room_id', $params['room_id'])->pluck('id')->toArray();
    
            return $this->switche->with('type_device')->Status()->FilterByUserId(Auth::user()->id)->whereIn('device_id', $list_device)->orderBy($sort_by, $order)->paginate($page_length);
        }
        if(isset($params['device_id'])){
    
            return $this->switche->with('type_device')->Status()->FilterByUserId(Auth::user()->id)->where('device_id', $params['device_id'])->orderBy($sort_by, $order)->paginate($page_length);
        }

        return $this->switche->with('type_device')->Status()->FilterByUserId(Auth::user()->id)->orderBy($sort_by, $order)->paginate($page_length);
        
    }

    /**
     * List all switche by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->switche->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all switche by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->switche->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all switche names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->switche->all()->pluck('name')->all();
    }

    /**
     * Find switche and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return switche
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $switche = $this->findOrFail($id);

        if (in_array($switche->name, config('system.default_category'))) {
            throw ValidationException::withMessages(['message' => trans('device.default_cannot_be_deleted')]);
        }

        return $switche;
    }

    /**
     * Delete activity log.
     *
     * @param switche $switche
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(switche $switche)
    {
        try {
            return $switche->delete();
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
        return $this->switche->whereIn('id', $ids)->delete();
    }
}
