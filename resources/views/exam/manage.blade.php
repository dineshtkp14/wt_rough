<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <script>
        BASE_URL="<?php  echo url(''); ?>";
    </script>
</head>
<body>
    

<h3>Exams</h3>
<input type="text" id="exam_search"  placeholder="Search for...">
<div class="x_content">
  <table class="table table-striped table-bordered" style="width: 50%;">
    <thead>
      <tr>
        <th class="exam_sorting" data-sorting_type="asc" data-column_name="id" style="cursor: pointer">Id</th>
        <th class="exam_sorting" data-sorting_type="asc" data-column_name="title" style="cursor: pointer">Name<span id="name_icon">Title</th>
        <th>Exam Date</th>
      </tr>
    </thead>
    <tbody>
        @foreach($exams as $exam)
        <tr>
          <td>{{$exam->id}}</td>
          <td>{{$exam->name}}</td>
          <td>{{$exam->email}}</td>
        </tr>
@endforeach
<tr class="exam_pagin_link">
<td colspan="6" align="center">{{$exams->links()}}</td>
</tr>
    </tbody>
  </table>
</div>
<input type="hidden" name="hidden_page" id="hidden_page" value="1" />
<input type="hidden" name="hidden_column_name" id="hidden_column_name" value="id" />
<input type="hidden" name="hidden_sort_type" id="hidden_sort_type" value="asc" />
</body>
</html>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

<script type="text/javascript">

$(document).ready(function(){
  
function fetch_data(search="") {
  $.ajax({
     url:BASE_URL+"/exam_manage_ajax?search="+search,
     success:function(data){
      $('.x_content tbody').html(data);
     }
  })
 }

 $(document).on('keyup', '#exam_search', function(){
    var search = $('#exam_search').val();
    fetch_data(search);
 });


});

</script>