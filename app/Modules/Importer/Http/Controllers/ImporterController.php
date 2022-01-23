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
class ImporterController extends Controller
{
    /**
     * Importer repository
     *
     * @var ImporterRepository
     */
    private $importerRepository;

    /**
     * Set repository and apply auth filter
     *
     * @param ImporterRepository $importerRepository
     */
    public function __construct(ImporterRepository $importerRepository)
    {

        $this->importerRepository = $importerRepository;
    }

    /**
     * Return list of Importer
     *
     * @param Config $config
     *
     * @return Response
     */
    public function index(Config $config)
    {

        return view('start.raport', ['titleRaport' => '']);
    }

    /**
     * Display the specified Importer
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $this->checkPermissions(['importer.show']);
        $id = (int) $id;

        return response()->json($this->importerRepository->show($id));
    }

    /**
     * Return module configuration for store action
     *
     * @return Response
     */
    public function create()
    {
        $this->checkPermissions(['importer.store']);
        $rules['fields'] = $this->importerRepository->getRequestRules();

        return response()->json($rules);
    }

    /**
     * Store a newly created Importer in storage.
     *
     * @param ImporterRequest $request
     *
     * @return Response
     */
    public function store(ImporterRequest $request)
    {
        $this->checkPermissions(['importer.store']);
        $model = $this->importerRepository->create($request->all());

        return response()->json(['item' => $model], 201);
    }

    /**
     * Display Importer and module configuration for update action
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $this->checkPermissions(['importer.update']);
        $id = (int) $id;

        return response()->json($this->importerRepository->show($id, true));
    }

    /**
     * Update the specified Importer in storage.
     *
     * @param ImporterRequest $request
     * @param  int $id
     *
     * @return Response
     */
    public function update(ImporterRequest $request, $id)
    {
        $this->checkPermissions(['importer.update']);
        $id = (int) $id;

        $record = $this->importerRepository->updateWithIdAndInput(
            $id,
            $request->all()
        );

        return response()->json(['item' => $record]);
    }

    /**
     * Remove the specified Importer from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $this->checkPermissions(['importer.destroy']);
        App::abort(404);
        exit;

        /* $id = (int) $id;
        $this->importerRepository->destroy($id); */
    }
    public function raport(ImporterRepository $ImporterRepo, $filename, $path)
    {
        //  The report function gets the path of the file and the name of the file to be analyzed. 
        //  Then it checks if the file contains the id: 
        // -OpenTickets
        // -AllTickets
        // -PaperworkTickets
        // After correctly downloading the data, it saves it to DB work_order and generates a csv file 
        //  and sends information to import_log about the course of the transaction 

        libxml_use_internal_errors(true);
        $urlFile = $path . '/' . $filename;
        if (!empty($urlFile)) {
            $html = file_get_html($urlFile);
        } else {
            return view('start.raport', ['titleRaport' => '', 'filename' => $filename]);
        }
        $raportLog = new ImporterLog;
        $raportLog->type = 'insert';
        $raportLog->run_at = 'ok';


        $idChoiceArr[1] = 'OpenTickets';
        $idChoiceArr[2] = "AllTickets";
        $idChoiceArr[3] = "PaperworkTickets";
        $data[] = ['ticket_id', 'entity_id',  'rcvd_date', 'urgency', 'category', 'story_name', 'type'];
        $dirname = 'raports';
        if (!is_dir($dirname)) {
            mkdir($dirname);
        }
        $date = time();


        $raportLog->entries_processed = $filename;


        $raportTitle = 'raport_' . date('d-m-Y_His', $date) . '.csv';
        $raport = $dirname . '/' . $raportTitle;
        $fp = fopen($raport, 'w');
        foreach ($data as $field) {
            fputcsv($fp, $field, ',');
        }
        $raportLog->entries_created = $raportTitle;



        $k = 0;
        $arr[] = 0;
        $Ticket = null;
        $entity = null;
        $rcvdDate = null;
        $urgency = null;
        $category = null;
        $storyName = null;

        foreach ($idChoiceArr as $TicketV) {

            $idChoice = "ctl00_ctl00_ContentPlaceHolderMain_MainContent_TicketLists_" . $TicketV . "_ctl00";
            if ($html->find("table#$idChoice thead tr th")) {
                $arr = $html->find("table#$idChoice thead tr th");
            }

            for ($i = 0; $i < count($arr); $i++) {

                if ($html->find("table#$idChoice thead tr th", $i) != null) {
                    $info2[$k][0][$i] = $html->find("table#$idChoice thead tr th", $i)->plaintext;
                }
            }

            $info2[$k][0][$i] = 'Entityid';
            $c = count($html->find("table#$idChoice tbody tr"));
            for ($j = 0; $j < $c; $j++) {
                $idChoiceE = $idChoice . '__' . $j;

                $i = 0;
                foreach ($html->find("tr#$idChoiceE td") as $element) {

                    $info2[$k][$j + 1][$i] = $element->plaintext;
                    $i++;
                }
                if ($html->find("tr#$idChoiceE a", 0) != null) {
                    $entityid = $html->find("tr#$idChoiceE a", 0)->href;
                    $info2[$k][$j + 1][$i] = substr($entityid, strpos($entityid, 'id=') + 3);
                } else {
                    break;
                }
            }

            $k++;
        }


        for ($i = 0; $i < count($info2); $i++) {
            for ($j = 0; $j < count($info2[$i]); $j++) {
                $s = 0;

                foreach ($info2[$i][0] as $ch) {

                    switch ($ch) {
                        case 'Ticket':
                            $Ticket = $s;
                            break;
                        case 'Rcvd Date':
                            $rcvdDate = $s;
                            break;
                        case 'Category':
                            $category = $s;
                            break;
                        case 'Store Name':
                            $storyName = $s;
                            break;
                        case 'Entityid':
                            $entity = $s;
                            break;
                        case 'Urgency':
                            $urgency = $s;
                            break;
                        default:
                            break;
                    }
                    $s++;
                }


                if (!isset($Ticket)) {
                    $raportLog->run_at = 'brak danych o Ticket';
                    $raportLog->save();
                    return view('start.raport', ['titleRaport' => $raportTitle, 'filename' => $filename]);
                }
                $Ticket = $info2[$i][$j][$Ticket];

                $category = $info2[$i][$j][$category];
                $storyName = $info2[$i][$j][$storyName];
                if (!empty($urgency)) {
                    $urgency = $info2[$i][$j][$urgency];
                } else {
                    $urgency = '';
                }
                $rcvdDate = date('Y-m-d', strtotime($info2[$i][$j][$rcvdDate]));
                $entity = $info2[$i][$j][$entity];
                $exist = $ImporterRepo->getTicket($Ticket);
                if ($j != 0 && !isset($exist)) { //checking if the record already exists in the database 

                    $workOrder = new WorkOrder;
                    $workOrder->work_order_number = $Ticket;
                    $workOrder->category = $category;
                    $workOrder->fin_loc =  $storyName;
                    $workOrder->priority = $urgency;
                    $workOrder->received_date = $rcvdDate;
                    $workOrder->external_id = $entity;
                    $workOrder->save();
                    $data[] = [$Ticket, $entity,  $rcvdDate, $urgency, $category, $storyName, 'insert record'];

                    $fp = fopen($raport, 'w');
                    foreach ($data as $field) {
                        fputcsv($fp, $field, ',');
                    }
                } else {
                    if ($j != 0) {
                        $data[] = [$Ticket, $entity,  $rcvdDate, $urgency, $category, $storyName, 'record exist'];

                        $fp = fopen($raport, 'w');
                        foreach ($data as $field) {
                            fputcsv($fp, $field, ',');
                        }
                    }
                }
            }
        }

        //Save raport log
        $raportLog->save();
        return view('start.raport', ['titleRaport' => $raportTitle, 'filename' => $filename]);
    }



    public function uploadFile(Request $request)
    {

        // Validation
        $request->validate([
            'file' => 'required|mimes:html|max:2048'
        ]);

        if ($request->file('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();

            // File upload location
            $location = 'files';

            // Upload file
            $file->move($location, $filename);
        } else {
        }

        return redirect()->action('App\Modules\Importer\Http\Controllers\ImporterController@raport', ['filename' => $filename, 'path' => $location]);
    }



    public function add()
    {
        return view('start.insert', ['titleRaport' => '']);
    }
    public function addTwo()
    {
        return view('start.insertTwo', ['titleRaport' => '']);
    }
}
