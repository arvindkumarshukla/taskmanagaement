<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
class TaskController extends Controller
{
    public function index()
    {
        return view('taskview');
    }
    public function store(Request $request)
    {
        $checkTask = Task::where('name',$request->name)->first();
        if(!empty($checkTask)){
            return json_encode(['status'=>true,'msg'=>'Task already created']);
        }
        $task = new Task;
        $task->name = $request->name;
        $task->status = 0;
        $task->save();
        $allTasks = Task::where('status',0)->get();

        // send mail 
        $this->sendTaskMail('shuklaarvindrec@gmail.com','Task Created','Task '.$task->name.' created successfully');
        return json_encode(['status'=>true,'msg'=>'Task created successfully','data'=>$allTasks]);
    }

    public function taskUpdate(Request $request)
    {
        $task = Task::where('id',$request->id)->first();
        $task->status = 1;
        $task->save();
        $allTasks = Task::where('status',0)->get();

        //send mail
        $this->sendTaskMail('shuklaarvindrec@gmail.com','Task Completed','Task '.$task->name.' updated successfully');

        return json_encode(['status'=>true,'msg'=>'Task completed successfully','data'=>$allTasks]);
    }

    public function showAllTask(Request $request)
    {
        $allTasks = Task::get();
        return json_encode(['status'=>true,'msg'=>'Fetch task data successfully','data'=>$allTasks]);
    }

    public function getPendingTasks(Request $request)
    {
        $allTasks = Task::where('status',0)->get();
        return json_encode(['status'=>true,'msg'=>'Fetch task data successfully','data'=>$allTasks]);
    }

    public function deleteTask(Request $request)
    {
        $tasks = Task::where('id',$request->id)->first();
        $tasks->delete();
        $allTasks = Task::get();
        return json_encode(['status'=>true,'msg'=>'Task deleted successfully','data'=>$allTasks]);
    }


    public function sendTaskMail($email,$subject,$emailBody)
    {
        $mail = new PHPMailer(true);
        try {
            $mail->SMTPDebug = 0;
            $mail->isSMTP();
            $mail->Host = env('MAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = env('MAIL_USERNAME');
            $mail->Password = env('MAIL_PASSWORD');
            $mail->Port = env('MAIL_PORT');
    
            $mail->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
            $mail->addAddress($email);
    
            $mail->isHTML(true);
    
            $mail->Subject = $subject;
            $mail->Body = $emailBody;
    
            if( !$mail->send() ) {

                $res = ['status'=>'error','msg'=>"Email not sent.",'errormsg'=>$mail->ErrorInfo];
                return json_encode($res);
            }
                
            else {
                $res = ['status'=>'success','msg'=>"Email sent."];
                return json_encode($res);
            }
    
        } catch (Exception $e) {
            $res = ['status'=>'error','msg'=>"Email not sent."];
            return json_encode($res);
        }
    }

    public function mailSend(){
        $data = $this->sendTaskMail('shuklaarvindrec@gmail.com','Test','test');
        print_r($data);
    }
}