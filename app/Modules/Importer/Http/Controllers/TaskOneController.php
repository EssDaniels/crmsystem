<?php

namespace App\Modules\Importer\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Modules\Importer\Repositories\ImporterRepository;
use Illuminate\Config\Repository as Config;
use App\Modules\Importer\Http\Requests\ImporterRequest;
use Illuminate\Http\Response;
use App;
use DOMDocument;
use App\Modules\WorkOrder\Models\WorkOrder;
use Illuminate\Http\Request;
use App\Modules\Importer\Models\ImporterLog;
use Session;

/**
 * Class ImporterController
 *
 * @package App\Modules\Importer\Http\Controllers
 */
class TaskOneController extends Controller
{
    public function taskOne()
    {
        $urlFile = 'file:///C:/xampp/htdocs/rekrutacjafriendlysolutionscorpjrphpdeveloperzada/wo_for_parse[92].html';

        $html = new DOMDocument();
        $html->loadHTMLFile($urlFile);
        $customer = $html->getElementById('customer')->textContent;
        $trade = $html->getElementById('trade')->textContent;
        $date =  $html->getElementById('scheduled_date')->textContent;
        $poNumber = $html->getElementById('po_number')->textContent;
        $woNumber = $html->getElementById('wo_number')->textContent;
        $nte = $html->getElementById('nte')->textContent;
        $locationAddress = $html->getElementById('location_address')->textContent;
        $phone = $html->getElementById('location_phone')->textContent;
        $customer = trim($customer);
        $trade = trim($trade);
        $date = trim($date);
        $date = preg_replace('!\s+!', ' ', $date);
        $date = strtotime($date);
        $date = date('Y-m-d H:i', $date);
        $poNumber = trim($poNumber);
        $woNumber = trim($woNumber);
        $nte = trim($nte);
        $nte = preg_replace('/[^0-9. ]/', '', $nte);
        $nte = str_replace('.', ',', $nte);
        $locationAddress = trim($locationAddress);
        $streetName = substr($locationAddress, 0, strpos($locationAddress, '  '));
        $streetName = trim($streetName);
        $adress = trim(substr($locationAddress, strpos($locationAddress, '  ')));
        $adress = explode(" ", $adress);
        $cityCode = $adress[count($adress) - 1];
        $state = $adress[count($adress) - 2];
        for ($i = 0; $i < count($adress) - 2; $i++) {
            $city[] = $adress[$i];
        }
        $city = implode(' ', $city);
        $phone = trim($phone);
        $data = [['customer', 'trade', 'scheduled_date', 'po_number', 'wo_number', 'nte', 'street', 'city', 'state', 'city_code', 'phone'], [$customer, $trade, $date, $poNumber, $woNumber, $nte, $streetName, $city,  $state, $cityCode, $phone]];

        $fp = fopen('parser.csv', 'w');
        foreach ($data as $field) {
            fputcsv($fp, $field, ',');
        }

        return view('start.raport', ['titleRaport' => '', 'filename' => '']);
    }
}
