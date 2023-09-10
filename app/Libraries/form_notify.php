<?php
namespace App\Libraries;
use Illuminate\Support\Facades\Mail as Mail;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\users;

class form_notify
{
    protected $model;
    protected $addresats_field;
    public function __construct($model,$message='',$addresat_field='can_change_data')
    {
        $this->model = $model;
        $this->addresats_field = $addresat_field;
    }
    public function send()
    {
        if( strlen($this->model->{$this->addresats_field})<1) return false;
        $addresses = json_decode($this->model->{$this->addresats_field});
        $srok = new Carbon($this->model->expared);
        foreach($addresses as $addr)
        {
            if(isset($addr->structure_id)) 
            {
                $this->sendmail($addr->email,'Заполнение отчета!','Пожалуйста заполните отчет '.$this->model->name.'!  Срок заполнения: '.$srok->format('d.m.Y').'. Ссылка на заполнение: '.url('/').'/edit_report?id='.$this->model->id);
            }
            else 
            {
                $users = users::where('structure_id','=',$addr->id)->get();
                foreach ($users as $user) {
                    $this->sendmail($user->email,'Заполнение отчета!','Пожалуйста заполните отчет '.$this->model->name.'!  Срок заполнения: '.$srok->format('d.m.Y').'. Ссылка на заполнение: '.url('/').'/edit_report?id='.$this->model->id);
                }
            }
        }
        return true;
    }
    protected function sendmail($addresat,$s_teme,$s_message)
    {
        if(strlen($s_message)<1) return false;
        if(!preg_match("/^.*@.*\./",$addresat))  return false;

       // return true;
        Mail::send(['text'=>'mail'], ['name'=>$s_message], function($message) use ($addresat,$s_message,$s_teme) {
            $message->to($addresat,'')->subject($s_teme);
            $message->from('webservice@spf.054.pfr.ru','webservice');
         });
    }
}