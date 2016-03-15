<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Src\Company\CompanyRepository;
use App\Src\Company\CompanyTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class CompanyController extends Controller
{

    /**
     * @var Manager
     */
    private $fractal;
    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * CategoryController constructor.
     * @param Manager $fractal
     * @param CompanyRepository $companyRepository
     */
    public function __construct(Manager $fractal, CompanyRepository $companyRepository)
    {
        $this->fractal = $fractal;
        $this->companyRepository = $companyRepository;
    }

    public function index()
    {
    }

    public function show($id, CompanyTransformer $companyTransformer)
    {
        $company = $this->companyRepository->model->with(['thumbnail'])->find($id);

        $item = new Item($company, $companyTransformer);

        $response = $this->fractal->createData($item)->toArray();

        return response()->json($response);
    }

}
