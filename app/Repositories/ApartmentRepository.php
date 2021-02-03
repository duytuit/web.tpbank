<?php

namespace App\Repositories;

use App\Models\apartment;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class ApartmentRepository
{
    /**
     * @var apartment
     */
    protected $apartment;

    /**
     * Instantiate a new Repository instance.
     *
     * @param apartment $apartment
     */
    public function __construct(apartment $apartment)
    {
        $this->apartment = $apartment;
    }

    /**
     * Get all apartment
     *
     * @return array
     */
    public function getAll()
    {
        return $this->apartment->withCount('apartments')->get();
    }

    /**
     * List all apartment names
     *
     * @return array
     */
    public function list()
    {
        return $this->apartment->all()->pluck('name', 'id')->all();
    }

    /**
     * Find apartment by Id
     *
     * @param int|null $id
     *
     * @return Apartment
     * @throws ValidationException
     */
    public function findOrFail($id = null)
    {
        $apartment = $this->apartment->with('user')->Status()->FilterByUserId(Auth::user()->id)->find($id);

        if (!$apartment) {
            throw ValidationException::withMessages(['message' => trans('apartment.could_not_find')]);
        }

        return $apartment;
    }

    /**
     * Get apartment by user_id
     *
     * @param string|null $user_id
     *
     * @return apartment
     */
    public function filterByUserId($params)
    {
        $user_id = Auth::user()->id;
        $sort_by = isset($params['sort_by']) ? $params['sort_by'] : 'updated_at';
        $order = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->apartment->Status()->FilterByUserId($user_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * List all apartment by name where given name is not included
     *
     * @param array|null $names
     *
     * @return array
     */
    public function listExceptName($names = [])
    {
        return $this->apartment->whereNotIn('name', $names)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all apartment by ids
     *
     * @param array $ids
     *
     * @return array
     */
    public function listNameById($ids = [])
    {
        $ids = is_array($ids) ? $ids : ($ids) ? [$ids] : [];

        return $this->apartment->whereIn('id', $ids)->get()->pluck('name')->all();
    }

    /**
     * List all apartment names only
     *
     * @return array
     */
    public function listName()
    {
        return $this->apartment->all()->pluck('name')->all();
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
        
        return $this->apartment->where('user_id',Auth::user()->id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Find apartment and check if it can be deleted or not.
     *
     * @param int $id
     *
     * @return apartment
     * @throws ValidationException
     */
    public function deletable($id)
    {
        $apartment = $this->findOrFail($id);

        if (in_array($apartment->name, config('system.default_category'))) {
            throw ValidationException::withMessages(['message' => trans('apartment.default_cannot_be_deleted')]);
        }

        return $apartment;
    }

    /**
     * Delete activity log.
     *
     * @param apartment $apartment
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(apartment $apartment)
    {
        try {
            return $apartment->delete();
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
        return $this->apartment->whereIn('id', $ids)->delete();
    }
}
