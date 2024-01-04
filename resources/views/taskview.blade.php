<!DOCTYPE html>
<html>
<head>
    <title>Task management</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
  <div class="container col-12 mt-4">
  <div class="card">
    <div class="card-header bg-info font-weight-bold">
      <div class="row">
        <div class="col-6 text-left"><h2>To do List</h2></div>
        <div class="col-6 text-right"><button class="btn btn-warning text-light" onclick="showAllData()"><h4>Show All Tasks</h4></button></div>
      </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="form-group col-8">
                <input placeholder="Please enter a task name" type="text" name="taskName" id="taskName" class="form-control h-100" required="">
            </div>
            <div class="form-group col-4">
                <button class="btn btn-success text-light" id="createTask" onclick="createTask()"><h4>Enter</h4></button>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
                <tr>
                <th scope="col-5" width="40%">Name</th>
                <th scope="col-2" width="20%">Status</th>
                <th scope="col-5" width="40%">Action</th>
                </tr>
            </thead>
            <tbody id="pendingTask">
                
            </tbody>
        </table>

        <table class="table table-bordered" id="taskTable" style="display:none">
            <thead>
              <tr>
                <th scope="col-5" colspan="3" class="text-center">All Task</th>
              </tr>
              <tr>
                <th scope="col-5" width="40%">Name</th>
                <th scope="col-2" width="20%">Status</th>
                <th scope="col-5" width="40%">Action</th>
              </tr>
            </thead>
            <tbody id="allTask">
                
            </tbody>
        </table>
    </div>
  </div>
</div>  
</body>

<script>
  function createTask(){
    taskName = $('#taskName').val();
    if($.trim(taskName) == ''){
      alert('Please enter task name');
      return;
    }
    $("#createTask"). attr("disabled", true);
    $.ajax({
      url: "{{url('store')}}",
      type: "POST",
      data: {"name":taskName,"_token": "{{ csrf_token() }}"},
      success: function( response ) {
        
        $('#taskName').val('');
        $("#createTask"). attr("disabled", false);
        data = JSON.parse(response)
        if(data.msg == 'Task already created'){
          Swal.fire({
            position: "top-center",
            icon: "error",
            title: "Oops...",
            text: data.msg,
          });
          return;
        }
        else{
          Swal.fire({
            position: "top-center",
            icon: "success",
            title: data.msg,
            showConfirmButton: false,
            timer: 1500
          });
        }
        data = data.data;
        
        html = preparePendingHtml(data);
        $('#pendingTask').html(html);
      }
   });
  }

  function showPendingData(){
    $.ajax({
      url: "{{url('getPendingTasks')}}",
      type: "GET",
      success: function( response ) {
        data = JSON.parse(response)
        data = data.data;
        html = preparePendingHtml(data);
        $('#pendingTask').html(html);
      }
   });
  }

  function showAllData(){
    $.ajax({
      url: "{{url('getAllTasks')}}",
      type: "GET",
      success: function( response ) {
        data = JSON.parse(response)
        data = data.data;
        html = prepareAllHtml(data);
        $('#allTask').html(html);
        $('#taskTable').show();
      }
   });
  }

  showPendingData();

  function preparePendingHtml(data){
    html="<tr><td colspan='3'>No Pending Task</td></tr>";
    if(data.length>0){
      html = '';
      for(i=0;i<data.length;i++){
        html += '<tr><td>'+data[i]['name']+'</td><td><span class="badge badge-warning">Pending</span></td><td><input class="clickToComplete" onclick="clickToComplete('+data[i]['id']+')" type="checkbox" value="'+data[i]['id']+'"></td></tr>';
      }
    }
    return html;
  }

  function prepareAllHtml(data){
    html="<tr><td colspan='3'>No Pending Task</td></tr>";
    if(data.length>0){
      html = '';
      for(i=0;i<data.length;i++){
        status = '<span class="badge badge-warning">Pending</span>';
        if(data[i]['status'] == 1){
          status = '<span class="badge badge-success">Completed</span>';
        }
        html += '<tr><td>'+data[i]['name']+'</td><td>'+status+'</td><td><button class="btn btn-danger" onclick="clickToDelete('+data[i]['id']+')">Delete</button></td></tr>';
      }
    }
    return html;
  }

  function clickToComplete(id) {
    $.ajax({
      url: "{{url('taskUpdate')}}",
      type: "POST",
      data: {"id":id,"_token": "{{ csrf_token() }}"},
      success: function( response ) {
        data = JSON.parse(response)
        Swal.fire({
          position: "top-center",
          icon: "success",
          title: data.msg,
          showConfirmButton: false,
          timer: 1500
        });
        data = data.data;
        html = preparePendingHtml(data);
        $('#pendingTask').html(html);
      }
   });
  }

  function clickToDelete(id) {

    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
      if (result.isConfirmed) {
        $.ajax({
          url: "{{url('taskDelete')}}",
          type: "POST",
          data: {"id":id,"_token": "{{ csrf_token() }}"},
          success: function( response ) {
            data = JSON.parse(response)
            Swal.fire({
              title: "Deleted!",
              text: "Task has been deleted.",
              icon: "success"
            });
            data = data.data;
            html = prepareAllHtml(data);
            $('#allTask').html(html);
          }
        });
      }
    }) 
  }

  
</script>

</html>