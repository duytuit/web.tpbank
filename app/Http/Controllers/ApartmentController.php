<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ApartmentRepository;
use App\Models\apartment;
use App\Repositories\ActivityLogRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ApartmentRequest;

class ApartmentController extends Controller
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var ApartmentRepository
     */
    protected $apartmentRepo;

    /**
     * @var ActivityLogRepository
     */
    protected $activity;

    /**
     * @var string
     */
    protected $module = 'apartment';

    /**
     * Instantiate a new controller instance
     *
     * @param Request $request
     * @param ApartmentRepository $repo
     */
    public function __construct(Request $request, ApartmentRepository $apartmentRepo, ActivityLogRepository $activity)
    {
        $this->request = $request;
        $this->apartmentRepo = $apartmentRepo;
        $this->activity = $activity;
        //$this->middleware('permission:access-category');
    }

    /**
     * Get all Apartment by user_id
     *
     * @return LengthAwarePaginator
     */
    public function index(Request $request)
    {
        $apartment = $this->apartmentRepo->filterByUserId($this->request->all())->toArray();
        $apartment['message'] = trans('apartment.apartment_list');
        // list search with option parameter
        $apartment['option'] = [];
        return $this->success($apartment);
    }

    /**
     * Store Apartment
     *
     * @param ApartmentRepository $request
     *
     * @return JsonResponse
     */
    public function store(ApartmentRequest $request)
    {
        
        if (apartment::where(['name'=>request('name'),'user_id'=> Auth::user()->id])->exists()) {
            return $this->error(['message' => trans('apartment.exists')]);
        }

        try {

            $apartment = apartment::create([
                'user_id' => Auth::user()->id,
                'name' => request('name'),
                'image' => request('image') ?? null,
                'address' => request('address') ?? null
            ]); 

            $this->activity->record([
            'module' => $this->module,
            'module_id' => $apartment->id,
            'activity' => 'added'
            ]);

        } catch (QueryException $e) {
            throw $e;
        }
        return $this->success(['message' => trans('apartment.added')]);
    }

     /**
     * Update Apartment
     *
     * @param UserProfileRequest $request
     * @param int $id
     *
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws ValidationException
     */
    public function update(ApartmentRequest $request, $id)
    {
        $apartment = $this->apartmentRepo->findOrFail($id);
        if(!$apartment){
             return $this->error(null,trans('apartment.could_not_find'));
        }
        //$this->authorize('update', $apartment);

        try {
            
            $apartment->name = request('name');
            $apartment->image = request('image');
            $apartment->address = request('address');
            $apartment->status = request('status') ?? 1;
            $apartment->save();

        } catch (QueryException $e) {
            throw $e;
        }

        return $this->success(['message' => trans('apartment.updated')]);
    }

    /**
     * @param int $id
     *
     * @return \App\apartment
     * @throws ValidationException
     */
    public function show($id)
    {
        $apartment['data'] = $this->apartmentRepo->findOrFail($id);
        $apartment['message'] = trans('apartment.apartment_list');
        return $this->success($apartment);
    }

    /**
     * Delete apartment
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
            
            $apartment = $this->apartmentRepo->deletable($id);

            $this->activity->record([
                'module' => $this->module,
                'module_id' => $apartment->id,
                'activity' => 'deleted'
            ]);

            $this->apartmentRepo->delete($apartment);

        } catch (QueryException $e) {
            throw $e;
        }
        return $this->success(['message' => trans('apartment.deleted')]);
    }
}
