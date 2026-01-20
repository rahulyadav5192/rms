<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salary </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <!-- Add your CSS styles or link to an external stylesheet here -->
</head>
<body>


    <table class="table table-hover">
      <thead>
        <tr>
          <th scope="col">S. No</th>
          <th scope="col">Employee Id </th>
          <th scope="col">Name </th>
          <th scope="col">Department </th>
          <th scope="col">Total Working Hours </th>
        </tr>
      </thead>
      <tbody>
          @foreach($all as $key=>$d)
        <tr>
          <th scope="row">{{$key+1}}</th>
          <td>{{$d['emp_id']}}</td>
        <td>{{$d['emp']}}</td>
        <td>{{$d['department']}}</td>
        <td>{{ number_format($d['salary'], 2) }} </td> 
        </tr>
        @endforeach 
        <tr>
      </tbody>
    </table>


</body>
</html>