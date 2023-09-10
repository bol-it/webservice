<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Auth;
use stdClass;
use Image;
use DB;
use SplFileObject;
use App\Models\web_forms;
use App\Models\results_web_forms;
use App\Libraries\BCSVWriter;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use \PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpWord\PhpWord;



class FormsController extends Controller
{
    public function forms()
    {
        return view('forms');
    }

    public function save_form(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|',
            'form_scheme' => 'required|json',
        ], $messages = [
            'required' => 'Отсутствует обязательный параметр :attribute.',
            'numeric' => 'Параметр :attribute не является числом.',
            'json' => 'Параметр :attribute не является JSON.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
            return $result;
        }
        $id = $request->input('id');
        $form_scheme =  json_decode($request->input('form_scheme'));
        $web_forms = NULL;
        if ($id == -1) {
            $web_forms = new web_forms();
            $new = true;
        }
        else if ($id > 0) {
            $web_forms = web_forms::find($id);
            $new = false;
            if (is_null($web_forms)) {
                $errors = 'Запись для измененния не найдена';
                $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
                return $result;
            }
        }
        else {
            $errors = 'Передан неправильный идентификатор';
            $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
            return $result;
        }
        $name = 'Название формы';
        if (property_exists($form_scheme->header, 'properties')) {
            if (property_exists($form_scheme->header->properties, 'label')) {
                $name = $form_scheme->header->properties->label;
            }
        }
        $web_forms->body = json_encode($form_scheme, JSON_UNESCAPED_UNICODE);
        $web_forms->name = $name;
        if ($web_forms->save()) {
            $result = ['error'=>'0', 'success'=>'1', 'message'=>'Запись успешно сохранена', 'new' => $new, 'id_forms' => $web_forms->id];
            return $result;
        }
        $result = ['error'=>'1', 'success'=>'0', 'message'=>'Ошибка при сохранении'];
        return $result;
    }

    public function get_forms(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage');
        if(!isset($itemsPerPage)) $itemsPerPage = 10;
        $sortBy = $request->input('sortBy');
        $sortDesc = $request->input('sortDesc');
        if ($sortBy == '') {
            $sortBy = 'web_forms.id';
            $sortDesc = 'asc';
        }
        else {
            $sortDesc == 'true' ? $sortDesc = 'desc' : $sortDesc = 'asc';
        }
        $search = $request->input('search');
        if(!isset($search)) $search = '';
        $web_forms = web_forms::select(
            'web_forms.id as id',
            'web_forms.name as name',
        )
        ->where(function ($query) use ($search) {
            $query->whereRaw("LOWER(web_forms.name) like '%".mb_convert_case($search, MB_CASE_LOWER, "UTF-8")."%'");
        })
        ->orderBy($sortBy,$sortDesc)
        ->paginate($itemsPerPage);
        return $web_forms;
    }

    public function get_form(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|integer|gt:0',
        ], $messages = [
            'required' => 'Отсутствует обязательный параметр :attribute.',
            'numeric' => 'Параметр :attribute не является числом.',
            'integer' => 'Параметр :attribute не является целым числом.',
            'gt' => 'Параметр :attribute должен быть больше 0.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
            return $result;
        }
        $id = $request->input('id');
        $web_forms = web_forms::find($id);

        if (!$web_forms) {
            $errors = 'Форма не найдена';
            $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
            return $result;
        }
        $edit_form = array('id' => $web_forms->id, 'body' => json_decode($web_forms->body), 'name' => $web_forms->name);
        return $edit_form;
    }

    public function delete_form(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric|integer|gt:0',
        ], $messages = [
            'required' => 'Отсутствует обязательный параметр :attribute.',
            'numeric' => 'Параметр :attribute не является числом.',
            'integer' => 'Параметр :attribute не является целым числом.',
            'gt' => 'Параметр :attribute должен быть больше 0.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
            return $result;
        }
        $id = $request->input('id');
        $web_forms = web_forms::find($id);

        if (!$web_forms) {
            $errors = 'Форма для удаления не найдена';
            $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
            return $result;
        }
        if ($web_forms->delete()) {
            $result = ['error'=>'0', 'success'=>'1', 'message'=>'Форма успешно удалена'];
            return $result;
        }
    }

    public function save_report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id_form' => 'required|numeric|integer|gt:0',
            'form_scheme' => 'required|json',
        ], $messages = [
            'required' => 'Отсутствует обязательный параметр :attribute.',
            'numeric' => 'Параметр :attribute не является числом.',
            'integer' => 'Параметр :attribute не является целым числом.',
            'gt' => 'Параметр :attribute должен быть больше 0.',
            'json' => 'Параметр :attribute не является JSON.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            $result = ['error'=>'1', 'success'=>'0', 'message'=> $errors];
            return $result;
        }


        $id_form = $request->input('id_form');

        $form_scheme =  json_decode($request->input('form_scheme'));

        $name = 'Название формы';
        if (property_exists($form_scheme->header, 'properties')) {
            if (property_exists($form_scheme->header->properties, 'label')) {
                $name = $form_scheme->header->properties->label;
            }
        }

        $results_web_forms = new results_web_forms();

        $results = [];

        foreach ($form_scheme->items as $item) {
            $value = '';
            if ($item->type == 'form_checkbox') {
                $value = implode(";", $item->value);
            }
            else {
                $value = $item->value;
            }
            $label = 'test';
            if (property_exists($item, 'properties')) {
                if (property_exists($item->properties, 'label')) {
                    $label = $item->properties->label;
                }
            }
            $result_item = (object) array('type' => $item->type, 'label' => $label, 'value' => $value);
            $results[] = $result_item;
        }

        $results_web_forms->web_form_id = $id_form;
        $results_web_forms->name = $name;
        $results_web_forms->body = json_encode($form_scheme, JSON_UNESCAPED_UNICODE);
        $results_web_forms->results = json_encode($results, JSON_UNESCAPED_UNICODE);
        $results_web_forms->save();

        if ($results_web_forms->save()) {
            $result = ['error'=>'0', 'success'=>'1', 'message'=>'Отчет успешно сохранен'];
            return $result;
        }
        $result = ['error'=>'1', 'success'=>'0', 'message'=>'Ошибка при сохранении'];
        return $result;
    }

    public function get_result_form(Request $request)
    {
        $itemsPerPage = $request->input('itemsPerPage');
        if(!isset($itemsPerPage)) $itemsPerPage = 10;
        $sortBy = $request->input('sortBy');
        $sortDesc = $request->input('sortDesc');
        if ($sortBy == '')
        {
            $sortBy = 'results_web_forms.id';
            $sortDesc = 'asc';
        }
        else {
            $sortDesc == 'true' ? $sortDesc = 'desc' : $sortDesc = 'asc';
        }
        $search = $request->input('search');
        if(!isset($search)) $search = '';
        $id = $request->input('id');

        $forms = web_forms::find($id);
        $rf = results_web_forms::select('results_web_forms.*')
        ->where('web_form_id','=',$id)
        ->where(function ($query) use ($search) {
            $query->whereRaw("JSON_SEARCH(results,'one','%".$search."%') is NOT null")->
            orwhereRaw("LOWER(results_web_forms.name) like '%".mb_convert_case($search, MB_CASE_LOWER, "UTF-8")."%'");
        })
        ->orderBy($sortBy,$sortDesc)
        ->paginate($itemsPerPage);
        $headers = array(array('text'=>'id','align'=>'left','value'=>'id','class'=>'px-2'),
            array('text'=>'Запрос','align'=>'left','value'=>'name','class'=>'px-2'),
        );
        $hd = json_decode($forms->body)->items;
        foreach ($hd as $value) {
            $headers[]=array('text'=>$value->properties->label,'align'=>'left','value'=>$value->properties->label, 'sortable' => false,'class'=>'px-2');
        }
        $data_array =[];
        foreach($rf as $result) {
            $data_row = array('id'=>$result->id,'web_form_id'=>$result->web_form_id,'name'=>$result->name);
            foreach (json_decode($result->results) as $field) {
                $data_row[$field->label] = $field->value;
            }
            $data_array[] = (object) $data_row;
        }
        return array('headers'=>$headers, 'data'=>$rf, 'items'=> $data_array);
    }

    public function get_excel($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT, array("options"=> array("min_range" => 1, "default" => NULL)));
        if (!$id) return response('Неправильный идентификатор', 400);
        $forms = web_forms::find($id);
        if (!$forms) return response('Результаты формы не найдены', 404);
        $results_web_forms = results_web_forms::where('web_form_id', '=', $id)->count();
        if ($results_web_forms == 0) return response('Результаты формы не найдены', 404);

        $csv = new BCSVWriter();
        $name_z = $forms->name;
        $headers = [];
        $array_index_date = [];
        $form_scheme =  json_decode($forms->body);
        foreach ($form_scheme->items as $item) {
            $label = NULL;
            if (property_exists($item, 'properties')) {
                if (property_exists($item->properties, 'label')) {
                    $label = $item->properties->label;
                }
            }
            if ($label) {
                $headers[$label] = $label;
                if ($item->type == 'form_date_field') {
                    $array_index_date[] = count($headers);
                }
            }
        }
        $csv->setHeader($headers);
        $csv->setArrayCheckOfLength('false');

        $results_forms = results_web_forms::select('results_web_forms.*')
        ->where('web_form_id','=',$id)
        ->chunk(500, function($items_results_forms) use($csv) {
            foreach ($items_results_forms as $item) {
                $items = [];
                $results = json_decode($item->results);
                foreach ($results as $result) {
                    $value = $result->value;
                    if ($result->type == 'form_date_field') $value = \PhpOffice\PhpSpreadsheet\Shared\Date::stringToExcel($value);//date_z
                    $items[$result->label] = $value;
                }
                $csv->add($items);
            }
        });

        $path = $csv->getPath();
        $namefileScv = $csv->getFilename();

        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        $spreadsheet = $reader->load($path.'/'.$namefileScv);
        $sheet = $spreadsheet->getActiveSheet();
        $highestColumn = $sheet->getHighestColumn();
        $sheet->insertNewRowBefore(1, 1);
        $sheet->mergeCells("A1:${highestColumn}1");
        $countRow = $sheet->getHighestDataRow();
        $sheet->setCellValue('A1',$name_z);
        $styleArray = [
            'font' => [
                'name' => 'Times New Roman',
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                ],
            ],
        ];

        $sheet->getStyle('A2:'.$highestColumn.$countRow)->applyFromArray($styleArray);
        $spreadsheet->getDefaultStyle()->getFont()->setName('Times New Roman');
        $sheet->getStyle('A1:'.$highestColumn.'1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:'.$highestColumn.'1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1:'.$highestColumn.'1')->getFont()->setSize(12);
        $sheet->getStyle('A2:'.$highestColumn.$countRow)->getAlignment()->setWrapText(true);
        $sheet->getStyle("A3:A${countRow}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        foreach ($array_index_date as $index_date) {
            $column = Coordinate::stringFromColumnIndex($index_date);
            $sheet->getStyle("${column}3:${column}${countRow}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY);
        }
        $sheet->getColumnDimension('A')->setWidth(11);
        $sheet->getColumnDimension('B')->setWidth(23);
        $sheet->getColumnDimension('C')->setWidth(23);
        foreach (range('D', $sheet->getHighestDataColumn()) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $namefileXlsx = $csv->getBasename('.csv').'xlsx';
        $filename = 'file.xlsx';
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
        $writer->save($path.'/'.$namefileXlsx);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        return response()->download( $path.'/'.$namefileXlsx, $filename )->deleteFileAfterSend(true);
    }

    public function get_word($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT, array("options"=> array("min_range" => 1, "default" => NULL)));
        if (!$id) return response('Неправильный идентификатор', 400);
        $forms = web_forms::find($id);
        if (!$forms) return response('Результаты запроса не найдены', 404);
        $results_web_forms = results_web_forms::where('web_form_id', '=', $id)->count();
        if ($results_web_forms == 0) return response('Результаты запроса не найдены', 404);


        $phpWord = new PhpWord();
        $paper = new \PhpOffice\PhpWord\Style\Paper();
        $name_z = $forms->name;
        $headers = [];
        $form_scheme =  json_decode($forms->body);
        foreach ($form_scheme->items as $item) {
            $label = NULL;
            if (property_exists($item, 'properties')) {
                if (property_exists($item->properties, 'label')) {
                    $label = $item->properties->label;
                }
            }
            if ($label) $headers[$label] = $label;
        }

        $section = $phpWord->createSection(array('pageSizeW' => $paper->getWidth(), 'pageSizeH' => $paper->getHeight(), 'orientation' => 'landscape', 'marginLeft' => 600,
            'marginRight' => 600, 'marginTop' => 600, 'marginBottom' => 600, 'layout' => \PhpOffice\PhpWord\Style\Table::LAYOUT_AUTO,
        ));
        $section->addText($name_z, array('name' => 'Times New Roman', 'size' => 14), array('align'=>'center'));

        $styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'cellMargin' => 80,);
        $cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');

        $phpWord->addTableStyle('Colspan Rowspan', $styleTable);
        $table = $section->addTable('Colspan Rowspan');
        $table->addRow();
        foreach ($headers as $header) {
            $table->addCell(1500, $cellRowSpan)->addText($header, array('name' => 'Times New Roman', 'size' => 12), array('align'=>'center', 'spaceAfter' => 0));
        }

        $results_web_forms = results_web_forms::select('results_web_forms.*')
        ->where('web_form_id','=',$id)
        ->chunk(500, function($items_results_forms) use($table, $cellRowSpan, $headers) {
            foreach ($items_results_forms as $item) {
                $table->addRow();
                $items = [];
                $results = json_decode($item->results);
                foreach ($results as $result) {
                    $value = $result->value;
                    if ($result->type == 'form_date_field') $value = Carbon::parse($value)->format('d.m.Y');
                    $items[$result->label] = $value;
                }
                foreach ($headers as $key => $value) {
                    if (array_key_exists($key, $items)) {
                        $table->addCell(1500, $cellRowSpan)->addText($items[$key],array('name' => 'Times New Roman', 'size' => 11),array('spaceAfter' => 0));
                    }
                    else {
                        $table->addCell(1500, $cellRowSpan)->addText('',array('name' => 'Times New Roman', 'size' => 11),array('spaceAfter' => 0));
                    }
                }
            }
        });
        $file = new SplFileObject(tempnam(sys_get_temp_dir(), rand()).'.docx', 'w+');
        $path = $file->getPath();
        $namefileDoc = $file->getFilename();
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $xmlWriter->save($path.'/'.$namefileDoc);
        $filename = 'file.docx';
        return response()->download( $path.'/'.$namefileDoc, $filename )->deleteFileAfterSend(true);
    }
}
