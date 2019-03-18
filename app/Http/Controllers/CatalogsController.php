<?php

namespace App\Http\Controllers;

use App\Http\Resources\CatalogResource;
use App\Models\Catalog;
use Illuminate\Http\Request;

/**
 * @group Catalogs
 *
 * Class CatalogsController
 *
 * @package App\Http\Controllers
 */
class CatalogsController extends Controller {

    /**
     * CatalogsController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:admin')->except('index');
    }

    /**
     * Index
     * Display a listing of the catalog resources.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return CatalogResource::collection(
            Catalog::all()
        );
    }

    /**
     * Store
     * Store a newly created catalog resource in storage.
     *
     * @bodyParam title string required The UNIQUE title of the catalog.
     * @bodyParam label string required The UNIQUE label of the catalog.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'title' => 'required|unique:catalogs',
            'label' => 'required|unique:catalogs'
        ]);

        $catalog = Catalog::create($validated);

        return $this->respondCreated(
            'یک کاتالوگ جدید ایجاد شد', new CatalogResource($catalog)
        );
    }


    /**
     * Update
     * Update a exists created catalog resource
     *
     * @bodyParam title string required The UNIQUE title of the catalog.
     * @bodyParam label string required The UNIQUE label of the catalog.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $validated = $this->validate($request, [
            'title' => 'required|unique:catalogs,title,' . $id,
            'label' => 'required|unique:catalogs,label,' . $id
        ]);

        Catalog::findOrFail($id)
            ->update($validated);

        return $this->respond('بروزرسانی با موفقیت انجام شد');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        $catalog = Catalog::findOrFail($id);

        return $catalog->hasCategory()
            ? $this->respondInternalError('به دلیل وجود چندین گروه در این کاتالوگ امکان حذف وجود ندارد')
            : $this->respondDeleted();
    }
}
