<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Task;

use PHPMailer\PHPMailer\Exception;

use App\Mail\MyMail;
use Illuminate\Support\Facades\Mail;

class TaskController extends Controller
{
    public function index()
    {
        try {
            return view('taskview');
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error rendering view: ' . $e->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function store(Request $request)
    {
        try {
            $checkTask = Task::where('name', $request->name)->first();

            if (!empty($checkTask)) {
                return response()->json([
                    'status' => true,
                    'msg' => 'Task already created',
                ]);
            }

            $task = new Task;
            $task->name = $request->name;
            $task->status = 0;
            $task->save();

            $allTasks = Task::where('status', 0)->get();

            // send mail 
            $this->sendTaskMail('shuklaarvindrec@gmail.com', 'Task Created', 'Task ' . $task->name . ' created successfully');

            return response()->json([
                'status' => true,
                'msg' => 'Task created successfully',
                'data' => $allTasks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error creating task: ' . $e->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function taskUpdate(Request $request)
    {
        try {
            $task = Task::where('id', $request->id)->firstOrFail();
            $task->status = 1;
            $task->save();
            
            $allTasks = Task::where('status', 0)->get();

            // send mail
            $this->sendTaskMail('shuklaarvindrec@gmail.com', 'Task Completed', 'Task ' . $task->name . ' updated successfully');

            return response()->json([
                'status' => true,
                'msg' => 'Task completed successfully',
                'data' => $allTasks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error completing task: ' . $e->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function showAllTask(Request $request)
    {
        try {
            $allTasks = Task::get();

            return response()->json([
                'status' => true,
                'msg' => 'Fetch task data successfully',
                'data' => $allTasks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error fetching all tasks: ' . $e->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function getPendingTasks(Request $request)
    {
        try {
            $allTasks = Task::where('status', 0)->get();

            return response()->json([
                'status' => true,
                'msg' => 'Fetch task data successfully',
                'data' => $allTasks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error fetching pending tasks: ' . $e->getMessage(),
                'data' => null,
            ]);
        }
    }

    public function deleteTask(Request $request)
    {
        try {
            $task = Task::findOrFail($request->id);
            $task->delete();

            $allTasks = Task::get();

            return response()->json([
                'status' => true,
                'msg' => 'Task deleted successfully',
                'data' => $allTasks,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'msg' => 'Error deleting task: ' . $e->getMessage(),
                'data' => null,
            ]);
        }
    }


    public function sendTaskMail($email,$subject,$emailBody)
    {
        try{
            Mail::to($email)->send(new MyMail($subject, $emailBody));
            return 'Email sent successfully!';
        }
        catch (\Exception $e) {
            // Handle the exception
            return 'Error sending email: ' . $e->getMessage();
        }
    }

    // public function mailSend(){
    //     $data = $this->sendTaskMail('shuklaarvindrec@gmail.com','Test','test');
    //     print_r($data);
    // }
}