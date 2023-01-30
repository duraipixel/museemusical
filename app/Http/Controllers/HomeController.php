<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Repositories\DashboardRepository;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    protected $dashboardRepository;
    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->middleware('auth');
        $this->dashboardRepository    = $dashboardRepository;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        /***
         *  1. Total Sale count
         *  2. Total product count
         *  3. Total Payments Amounts
         *  4. Total Success Orders
         *  5. Total Customers
         *  6. Total Failures
         *  7. New Customer n month
         *  8. Top Selling category
         *  9. Top Selling Product
         *  10. Recent Order
         */
        $total_order = $this->dashboardRepository->getOrderCount();
        $total_success_order = $this->dashboardRepository->getOrderCount( '', '', 'delivered' );
        $total_product = $this->dashboardRepository->getProductCount();
        $total_payment = $this->dashboardRepository->getTotalPaymentAmount();
        $total_customer = $this->dashboardRepository->getCustomerCount();
        $total_fail_order = $this->dashboardRepository->getOrderCount( '', '', 'cancelled' );
        $new_customer = $this->dashboardRepository->getNewCustomer();
        $top_selling_category = $this->dashboardRepository->getTopSellingCategory();
        $top_selling_product = $this->dashboardRepository->getTopSellingProduct();

        $recent_order = Order::orderBy('id', 'desc')->where('status', '!=', 'pending')->first();
        

        $params = array(
                   'total_order' => $total_order, 
                   'total_product' => $total_product, 
                   'total_payment' => $total_payment, 
                   'total_success_order' => $total_success_order, 
                   'total_customer' => $total_customer, 
                   'total_fail_order' => $total_fail_order, 
                   'new_customer' => $new_customer, 
                   'top_selling_category' => $top_selling_category, 
                   'top_selling_product' => $top_selling_product, 
                   'recent_order' => $recent_order, 
                );
        
        return view('platform.dashboard.home', $params);
    }
}
