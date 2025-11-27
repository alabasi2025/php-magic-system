<?php

namespace App\Genes\Cashiers\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Genes\Cashiers\Actions\CashierAction;
use App\Genes\Cashiers\Resources\CashierResource;
use App\Genes\Cashiers\Requests\CashierRequest;

/**
 * @class CashierController
 * @package App\Genes\Cashiers\Controllers
 * @brief المتحكم الخاص بإدارة عمليات الصرافين في نظام الصرافين (Cashiers Gene).
 *
 * هذا المتحكم مسؤول عن معالجة طلبات الـ HTTP المتعلقة بالصرافين.
 * يتم استخدام الـ Actions لفصل منطق الأعمال عن المتحكمات.
 */
class CashierController extends Controller
{
    /**
     * @var CashierAction
     */
    protected $cashierAction;

    /**
     * CashierController constructor.
     *
     * @param CashierAction $cashierAction
     */
    public function __construct(CashierAction $cashierAction)
    {
        $this->cashierAction = $cashierAction;
    }

    /**
     * @brief عرض قائمة بالصرافين. (Task 2029: Backend - Task 14)
     *
     * يتم استرجاع قائمة الصرافين وتنسيقها باستخدام CashierResource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        // TODO: Implement filtering, sorting, and pagination logic
        $cashiers = $this->cashierAction->getAllCashiers($request->all());

        // استخدام CashierResource لتنسيق البيانات
        return CashierResource::collection($cashiers);
    }

    // TODO: Add other CRUD methods (store, show, update, destroy) as required by subsequent tasks.
}